<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Consultora;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ConsultantService
{
    /**
     * Paginated listing of consultoras with subscription & active staff counts.
     */
    public function getPaginatedConsultants()
    {
        return Consultora::with(['suscripcionLogs' => fn ($q) => $q->whereNull('estado_anterior')->with('user')])
            ->withCount([
                'users as tecnicos_count' => fn ($q) => $q->whereIn('rol', ['tecnico', 'technician'])->where('estado', 'activo'),
                'users as gestores_count' => fn ($q) => $q->whereIn('rol', ['gestor', 'administrador'])->where('estado', 'activo'),
                'empresas',
            ])
            ->paginate(10);
    }

    /**
     * Create a new consultora with optional logo upload.
     */
    public function createConsultant(User $creator, Request $request, array $validated): Consultora
    {
        $data = collect($validated)->except('logo')->toArray();
        $data['created_by_user_id'] = $creator->id;

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos/consultoras', 'public');
        }

        return Consultora::create($data);
    }

    public function getEditData(Consultora $consultant): array
    {
        $primaryGestor = $consultant->primaryGestor;

        return [
            'consultant' => [
                ...$consultant->only(['id', 'nombre', 'cuit', 'max_tecnicos', 'description', 'fiscal_address', 'province', 'locality', 'email', 'phone']),
                'max_gestores' => $consultant->max_gestores ?? 2,
                'max_empresas' => $consultant->max_empresas ?? 5,
                'estado_suscripcion' => $consultant->estado_suscripcion ?? 'activa',
                'color_primary' => $consultant->color_primary ?? '#1e40af',
                'color_secondary' => $consultant->color_secondary ?? '#6b7280',
                'color_accent' => $consultant->color_accent ?? '#16a34a',
                'logo_path' => $consultant->logo_path ? asset('storage/'.$consultant->logo_path) : null,
            ],
            'primary_gestor' => $primaryGestor ? [
                ...$primaryGestor->only(['id', 'nombres', 'apellidos', 'email', 'telefono', 'n_doc', 'matricula', 'estado']),
                'must_change_password' => (bool) $primaryGestor->must_change_password,
            ] : null,
            'has_primary_gestor' => $primaryGestor !== null,
            'suscripcion_logs' => $this->formatSubscriptionLogs($consultant),
            'audit_logs' => $this->formatAuditLogs($consultant),
        ];
    }

    /**
     * Update consultora attributes and optional logo.
     */
    public function updateConsultant(Consultora $consultant, Request $request, array $validated): void
    {
        $data = collect($validated)->except('logo')->toArray();

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos/consultoras', 'public');
        }

        $consultant->update($data);
    }

    /**
     * Save or update a user inside a consultora, enforcing plan quotas.
     *
     * @return array{error_field?: string, error?: string, message?: string}
     */
    public function saveConsultantUser(Consultora $consultant, array $validated, ?int $userId): array
    {
        if ($userId) {
            $user = User::findOrFail($userId);
            abort_unless($user->consultora_id === $consultant->id, 403, 'Acceso no autorizado a este usuario.');
        }

        if ($validated['estado'] === 'activo') {
            $quotaError = $this->checkRoleQuota($consultant, $validated['rol'], $userId);
            if ($quotaError) {
                return ['error_field' => 'rol', 'error' => $quotaError];
            }
        }

        $data = [
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'email' => $validated['email'],
            'rol' => $validated['rol'],
            'estado' => $validated['estado'],
            'consultora_id' => $consultant->id,
        ];

        if (! $userId) {
            $plainPassword = Str::random(16);
            $data['password'] = Hash::make($plainPassword);
            $data['must_change_password'] = true;

            User::create($data);
            session()->flash('generated_password', $plainPassword);

            return ['message' => 'Usuario creado exitosamente. Se ha generado su contraseña temporal.'];
        }

        User::findOrFail($userId)->update($data);

        return ['message' => 'Usuario actualizado exitosamente.'];
    }

    /**
     * Create or update the primary gestor of a consultora.
     */
    public function savePrimaryGestor(Consultora $consultant, array $validated): string
    {
        $existingPrimary = $consultant->primaryGestor;
        $data = [
            'nombres' => $validated['nombres'], 'apellidos' => $validated['apellidos'], 'email' => $validated['email'],
            'telefono' => $validated['telefono'] ?? null, 'n_doc' => $validated['n_doc'] ?? null, 'matricula' => $validated['matricula'] ?? null,
            'consultora_id' => $consultant->id, 'rol' => User::ROLE_GESTOR, 'is_primary' => true, 'estado' => 'activo',
        ];

        if (! $existingPrimary) {
            $plainPassword = Str::random(16);
            $data['password'] = Hash::make($plainPassword);
            $data['must_change_password'] = true;
            User::create($data);
            session()->flash('generated_password', $plainPassword);

            return 'Gestor Principal creado exitosamente. Comuníquele su clave inicial de acceso.';
        }

        $existingPrimary->update($data);

        return 'Datos del Gestor Principal actualizados exitosamente.';
    }

    /**
     * Reset a user's password to a temporary random password and flash it.
     */
    public function resetTemporaryPassword(User $user): void
    {
        $plainPassword = Str::random(16);
        $user->update([
            'password' => Hash::make($plainPassword),
            'must_change_password' => true,
        ]);

        session()->flash('generated_password', $plainPassword);
    }

    private function checkRoleQuota(Consultora $consultant, string $rol, ?int $excludeUserId): ?string
    {
        if (! in_array($rol, ['tecnico', 'gestor'], true)) {
            return null;
        }
        $roles = $rol === 'tecnico' ? ['tecnico', 'technician'] : ['gestor', 'administrador'];
        $limit = $rol === 'tecnico' ? $consultant->max_tecnicos : $consultant->max_gestores;
        $label = $rol === 'tecnico' ? 'técnicos' : 'gestores';

        $count = User::where('consultora_id', $consultant->id)->whereIn('rol', $roles)->where('estado', 'activo')
            ->when($excludeUserId, fn ($q) => $q->where('id', '!=', $excludeUserId))->count();

        return $count >= $limit ? "Se ha alcanzado el límite de {$label} activos ({$limit}) para esta consultora." : null;
    }

    private function formatSubscriptionLogs(Consultora $consultant): array
    {
        $logs = $consultant->suscripcionLogs()->with('user:id,nombres,apellidos,email')->orderBy('created_at', 'desc')->get();
        $formattedLogs = [];
        $count = count($logs);

        for ($i = 0; $i < $count; $i++) {
            $current = $logs[$i];
            $startDate = $current->created_at;
            $endDate = ($i > 0) ? $logs[$i - 1]->created_at : now();
            $formattedLogs[] = [
                'id' => $current->id,
                'estado_anterior' => $current->estado_anterior,
                'estado_nuevo' => $current->estado_nuevo,
                'created_at' => $current->created_at?->toISOString(),
                'created_at_formatted' => $current->created_at ? $current->created_at->format('d/m/Y H:i') : 'N/A',
                'user' => $current->user ? ['name' => $current->user->nombres.' '.$current->user->apellidos, 'email' => $current->user->email] : null,
                'duration' => $startDate->diffForHumans($endDate, ['parts' => 2, 'join' => true, 'syntax' => CarbonInterface::DIFF_ABSOLUTE]),
                'is_current' => ($i === 0),
            ];
        }

        if ($count === 0) {
            $createdAt = $consultant->created_at ?? now();
            $formattedLogs[] = [
                'id' => 0,
                'estado_anterior' => null,
                'estado_nuevo' => $consultant->estado_suscripcion ?? 'activa',
                'created_at' => $createdAt->toISOString(),
                'created_at_formatted' => $createdAt->format('d/m/Y H:i'),
                'user' => null,
                'duration' => $createdAt->diffForHumans(now(), ['parts' => 2, 'join' => true, 'syntax' => CarbonInterface::DIFF_ABSOLUTE]),
                'is_current' => true,
            ];
        }

        return $formattedLogs;
    }

    private function formatAuditLogs(Consultora $consultant): array
    {
        $auditLogs = AuditLog::where('target_type', Consultora::class)->where('target_id', $consultant->id)
            ->with('user:id,nombres,apellidos,email,rol')->orderBy('created_at', 'desc')->get();

        $formatted = [];
        $auditCount = count($auditLogs);

        for ($i = 0; $i < $auditCount; $i++) {
            $current = $auditLogs[$i];
            $startDate = $current->created_at;
            $endDate = ($i > 0) ? $auditLogs[$i - 1]->created_at : now();
            $formatted[] = [
                'id' => $current->id,
                'action' => $current->action,
                'payload' => $current->payload,
                'created_at' => $current->created_at?->toISOString(),
                'created_at_formatted' => $current->created_at ? $current->created_at->format('d/m/Y H:i') : 'N/A',
                'user' => $current->user ? ['name' => $current->user->nombres.' '.$current->user->apellidos, 'email' => $current->user->email, 'rol' => $current->user->rol] : null,
                'duration' => $startDate ? $startDate->diffForHumans($endDate, ['parts' => 2, 'join' => true, 'syntax' => CarbonInterface::DIFF_ABSOLUTE]) : 'N/A',
                'is_current' => ($i === 0),
            ];
        }

        return $formatted;
    }
}
