<?php

namespace App\Services;

use App\Models\Consultora;
use App\Models\Empresa;
use App\Models\Provincia;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class CompanyService
{
    public function resolveConsultoraId(User $currentUser, ?int $empresaId, ?int $requestedConsultoraId): ?int
    {
        if (! $currentUser->isSuperAdmin()) {
            return $currentUser->consultora_id;
        }

        return $requestedConsultoraId ?? ($empresaId ? Empresa::find($empresaId)?->consultora_id : null);
    }

    public function getIndexData(User $currentUser, Request $request): array
    {
        $query = Empresa::query();

        if (! $currentUser->isSuperAdmin()) {
            $query->where('consultora_id', $currentUser->consultora_id);
        }

        if ($request->boolean('showDeleted')) {
            $query->onlyTrashed();
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', "%{$request->search}%")
                    ->orWhere('razon_social', 'like', "%{$request->search}%")
                    ->orWhere('cuit', 'like', "%{$request->search}%");
            });
        }

        return [
            'companies' => $query->withCount('sedes')->orderBy('nombre')->paginate(10)->withQueryString(),
            'filters' => $request->only(['search', 'showDeleted']),
        ];
    }

    public function findForEdit(User $currentUser, int|string $id): Empresa
    {
        $query = Empresa::query();

        if (! $currentUser->isSuperAdmin()) {
            $query->where('consultora_id', $currentUser->consultora_id);
        }

        return $query->findOrFail($id);
    }

    public function checkLimitForCreate(?int $consultoraId): ?string
    {
        if (! $consultoraId) {
            return null;
        }

        $consultora = Consultora::findOrFail($consultoraId);
        $count = Empresa::where('consultora_id', $consultoraId)->count();

        if ($count >= $consultora->max_empresas) {
            return 'Se ha alcanzado el límite de empresas clientes ('.$consultora->max_empresas.') para esta consultora.';
        }

        return null;
    }

    public function saveCompany(User $currentUser, array $data, ?int $id, ?int $consultoraId, ?UploadedFile $logo): string
    {
        if (! $currentUser->isSuperAdmin()) {
            $data['consultora_id'] = $consultoraId;
        }

        if ($logo) {
            $data['logo_path'] = $logo->store('logos', 'public');
        }

        if ($id) {
            $empresa = Empresa::findOrFail($id);
            $this->ensureTenancy($currentUser, $empresa);
            $empresa->update($data);

            return 'Empresa actualizada exitosamente.';
        }

        Empresa::create($data);

        return 'Empresa creada exitosamente.';
    }

    public function restoreCompany(User $currentUser, int|string $id): array
    {
        $company = Empresa::withTrashed()->findOrFail($id);
        $this->ensureTenancy($currentUser, $company);

        if ($company->consultora_id) {
            $consultora = Consultora::findOrFail($company->consultora_id);
            $count = Empresa::where('consultora_id', $company->consultora_id)->count();
            if ($count >= $consultora->max_empresas) {
                return ['ok' => false, 'message' => 'No se puede restaurar la empresa: Límite de empresas excedido.'];
            }
        }

        $company->restore();

        return ['ok' => true, 'message' => 'Empresa restaurada exitosamente.'];
    }

    public function getLocationsData(User $currentUser, Empresa $empresa, Request $request): array
    {
        $this->ensureTenancy($currentUser, $empresa);

        $query = $empresa->sedes()->with(['provincia', 'users']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre_sede', 'like', "%{$request->search}%")
                    ->orWhere('localidad', 'like', "%{$request->search}%")
                    ->orWhere('direccion', 'like', "%{$request->search}%");
            });
        }

        if ($request->boolean('showDeleted')) {
            $query->onlyTrashed();
        }

        $availableUsersQuery = User::whereIn('rol', ['tecnico', 'Técnico'])->orderBy('apellidos');
        if ($empresa->consultora_id) {
            $availableUsersQuery->where('consultora_id', $empresa->consultora_id);
        }

        return [
            'empresa' => $empresa,
            'locations' => $query->orderBy('nombre_sede')->get(),
            'provincias' => Provincia::orderBy('nombre')->get(),
            'availableUsers' => $availableUsersQuery->get(),
            'filters' => $request->only(['search', 'showDeleted']),
        ];
    }

    public function saveLocation(User $currentUser, Empresa $empresa, array $data, ?int $id): string
    {
        $this->ensureTenancy($currentUser, $empresa);

        if ($id) {
            $sede = Sede::findOrFail($id);
            $sede->update($data);

            return 'Sede actualizada.';
        }

        $empresa->sedes()->create($data);

        return 'Sede creada.';
    }

    public function restoreLocation(User $currentUser, int|string $id): void
    {
        $sede = Sede::withTrashed()->findOrFail($id);
        $this->ensureTenancy($currentUser, $sede->empresa);
        $sede->restore();
    }

    public function toggleUserAssignment(User $currentUser, Sede $sede, int $userId): string
    {
        $this->ensureTenancy($currentUser, $sede->empresa);

        if ($sede->users()->where('users.id', $userId)->exists()) {
            $sede->users()->detach($userId);

            return 'Técnico desvinculado.';
        }

        $sede->users()->attach($userId);

        return 'Técnico vinculado.';
    }

    public function ensureTenancy(User $currentUser, Empresa $empresa): void
    {
        if (! $currentUser->isSuperAdmin() && $empresa->consultora_id !== $currentUser->consultora_id) {
            abort(403, 'Acceso no autorizado.');
        }
    }
}
