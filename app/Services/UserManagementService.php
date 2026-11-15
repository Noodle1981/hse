<?php

namespace App\Services;

use App\Models\Consultora;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserManagementService
{
    /**
     * Paginated user listing scoped by consultora_id and optional filters.
     */
    public function getUsersData(User $currentUser, Request $request): array
    {
        $query = User::query()
            ->when(! $currentUser->isSuperAdmin(), fn ($q) => $q->where('consultora_id', $currentUser->consultora_id))
            ->when($request->search, function ($q, $search) {
                $q->where(function ($sq) use ($search) {
                    $sq->where('nombres', 'like', "%{$search}%")
                        ->orWhere('apellidos', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('matricula', 'like', "%{$search}%");
                });
            })
            ->when($request->rol, fn ($q, $rol) => $q->where('rol', $rol))
            ->when($request->trashed === 'true', fn ($q) => $q->onlyTrashed())
            ->orderBy('apellidos');

        $consultora = $currentUser->consultora;

        return [
            'users' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['search', 'rol', 'trashed']),
            'limits' => $consultora ? [
                'max_tecnicos' => $consultora->max_tecnicos,
                'current_tecnicos' => User::where('consultora_id', $consultora->id)->whereIn('rol', ['tecnico', 'technician'])->where('estado', 'activo')->count(),
                'max_gestores' => $consultora->max_gestores,
                'current_gestores' => User::where('consultora_id', $consultora->id)->whereIn('rol', ['gestor', 'administrador'])->where('estado', 'activo')->count(),
            ] : null,
        ];
    }

    /**
     * List technicians and companies scoped to the current user's consultora.
     */
    public function getTechniciansData(User $currentUser): array
    {
        $consultoraId = $currentUser->isSuperAdmin() ? null : $currentUser->consultora_id;

        $technicians = User::whereIn('rol', ['tecnico', 'technician'])
            ->when($consultoraId, fn ($q) => $q->where('consultora_id', $consultoraId))
            ->withCount('sedes')
            ->with('sedes.empresa')
            ->get();

        $empresas = Empresa::with('sedes')
            ->when($consultoraId, fn ($q) => $q->where('consultora_id', $consultoraId))
            ->get();

        return [
            'technicians' => $technicians,
            'empresas' => $empresas,
        ];
    }

    /**
     * Save (create or update) a consultora user while enforcing tenant isolation and plan quotas.
     *
     * @return array{error_field?: string, error?: string, message?: string}
     */
    public function saveUser(User $currentUser, Request $request, array $validated): array
    {
        $editingId = $request->input('id');
        $consultoraId = ! $currentUser->isSuperAdmin()
            ? $currentUser->consultora_id
            : ($request->input('consultora_id') ?? ($editingId ? User::find($editingId)?->consultora_id : null));

        if ($consultoraId && $validated['estado'] === 'activo') {
            $consultora = Consultora::findOrFail($consultoraId);
            $rol = $validated['rol'];

            if ($rol === 'tecnico') {
                $count = User::where('consultora_id', $consultoraId)
                    ->whereIn('rol', ['tecnico', 'technician'])
                    ->where('estado', 'activo')
                    ->when($editingId, fn ($q) => $q->where('id', '!=', $editingId))
                    ->count();
                if ($count >= $consultora->max_tecnicos) {
                    return ['error_field' => 'rol', 'error' => 'Se ha alcanzado el límite de técnicos activos ('.$consultora->max_tecnicos.') para esta consultora.'];
                }
            } elseif ($rol === 'gestor') {
                $count = User::where('consultora_id', $consultoraId)
                    ->whereIn('rol', ['gestor', 'administrador'])
                    ->where('estado', 'activo')
                    ->when($editingId, fn ($q) => $q->where('id', '!=', $editingId))
                    ->count();
                if ($count >= $consultora->max_gestores) {
                    return ['error_field' => 'rol', 'error' => 'Se ha alcanzado el límite de gestores activos ('.$consultora->max_gestores.') para esta consultora.'];
                }
            }
        }

        $data = [
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'email' => $validated['email'],
            'rol' => $validated['rol'],
            'matricula' => $validated['matricula'],
            'telefono' => $validated['telefono'],
            'n_doc' => isset($validated['n_doc']) && trim($validated['n_doc']) !== '' ? $validated['n_doc'] : null,
            'estado' => $validated['estado'],
        ];

        if (! $currentUser->isSuperAdmin()) {
            $data['consultora_id'] = $currentUser->consultora_id;
        }

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
            $data['must_change_password'] = true;
        }

        if ($editingId) {
            $user = User::findOrFail($editingId);
            $this->ensureTenancy($currentUser, $user);
            $user->update($data);

            return ['message' => 'Usuario actualizado exitosamente.'];
        }

        User::create($data);

        return ['message' => 'Usuario creado exitosamente.'];
    }

    /**
     * Soft-delete a user after tenancy check.
     */
    public function deleteUser(User $currentUser, User $target): void
    {
        $this->ensureTenancy($currentUser, $target);
        $target->update(['estado' => 'inactivo']);
        $target->delete();
    }

    public function restoreUser(User $currentUser, int|string $id): array
    {
        $user = User::withTrashed()->findOrFail($id);
        $this->ensureTenancy($currentUser, $user);

        if ($user->consultora_id) {
            $consultora = Consultora::findOrFail($user->consultora_id);
            $isTech = in_array($user->rol, ['tecnico', 'technician']);
            $isGestor = in_array($user->rol, ['gestor', 'administrador']);
            if ($isTech || $isGestor) {
                $roles = $isTech ? ['tecnico', 'technician'] : ['gestor', 'administrador'];
                $limit = $isTech ? $consultora->max_tecnicos : $consultora->max_gestores;
                $label = $isTech ? 'técnicos' : 'gestores';
                $activeCount = User::where('consultora_id', $user->consultora_id)->whereIn('rol', $roles)->where('estado', 'activo')->count();
                if ($activeCount >= $limit) {
                    return ['error' => "No se puede restaurar el usuario: Límite de {$label} excedido."];
                }
            }
        }

        $user->restore();
        $user->update(['estado' => 'activo']);

        return ['message' => 'Usuario restaurado exitosamente.'];
    }

    public function resetGestorUserPassword(User $currentUser, User $user): string
    {
        abort_unless($user->consultora_id === $currentUser->consultora_id, 403, 'Acceso no autorizado.');
        $plainPassword = Str::random(16);
        $user->update(['password' => Hash::make($plainPassword), 'must_change_password' => true]);
        session()->flash('generated_password', $plainPassword);
        session()->flash('generated_password_user', $user->full_name ?? $user->nombres.' '.$user->apellidos);

        return "Contraseña de {$user->nombres} {$user->apellidos} reseteada exitosamente.";
    }

    public function getGlobalStaffData(Request $request): array
    {
        $query = User::query()->whereNull('consultora_id')->whereIn('rol', [User::ROLE_SUPER_ADMIN, User::ROLE_VENDEDOR]);
        if ($request->search) {
            $query->where(fn ($q) => $q->where('nombres', 'like', "%{$request->search}%")->orWhere('apellidos', 'like', "%{$request->search}%")->orWhere('email', 'like', "%{$request->search}%"));
        }

        return [
            'staff' => $query->orderBy('apellidos')->orderBy('nombres')->paginate(10)->withQueryString(),
            'filters' => $request->only(['search']),
        ];
    }

    /**
     * Save (create or update) global staff member.
     */
    public function saveGlobalStaff(array $validated, ?int $userId, ?string $password): string
    {
        $data = [
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'email' => $validated['email'],
            'rol' => $validated['rol'],
            'estado' => $validated['estado'],
            'consultora_id' => null,
        ];

        if ($password) {
            $data['password'] = Hash::make($password);
        }

        if ($userId) {
            User::findOrFail($userId)->update($data);

            return 'Miembro del staff global actualizado exitosamente.';
        }

        User::create($data);

        return 'Miembro del staff global creado exitosamente.';
    }

    public function ensureTenancy(User $currentUser, User $target): void
    {
        if (! $currentUser->isSuperAdmin() && $target->consultora_id !== $currentUser->consultora_id) {
            abort(403, 'Acceso no autorizado.');
        }
    }
}
