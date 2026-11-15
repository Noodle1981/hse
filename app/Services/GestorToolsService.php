<?php

namespace App\Services;

use App\Models\ChecklistAssignment;
use App\Models\Consultora;
use App\Models\DynamicChecklist;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\User;
use App\Models\VisitCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GestorToolsService
{
    /**
     * Find a VisitCertificate by public signature token.
     */
    public function findVisitByToken(string $token): VisitCertificate
    {
        return VisitCertificate::with(['sede.empresa', 'user'])
            ->where('token_responsable', $token)
            ->firstOrFail();
    }

    /**
     * Store a public signature for a visit certificate token.
     */
    public function signPublicCertificate(VisitCertificate $visit, string $responsibleName, string $signature): void
    {
        $signatureValue = $signature;
        if (Str::startsWith($signature, 'data:image')) {
            $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $signature);
            $decoded = base64_decode($base64);
            if ($decoded !== false) {
                $fileName = 'signatures/public_'.Str::random(32).'.png';
                Storage::disk('public')->put($fileName, $decoded);
                $signatureValue = $fileName;
            }
        }

        $visit->update([
            'responsible_name' => $responsibleName,
            'signature' => $signatureValue,
            'date_firma_responsable' => now(),
            'token_used' => true,
            'status' => 'signed',
        ]);
    }

    /**
     * Build Gestor Assignment Dispatch Center data scoped by consultora.
     */
    public function getAssignmentsIndexData(User $user, Request $request): array
    {
        $consultoraId = $user->isSuperAdmin() ? null : $user->consultora_id;

        $assignments = ChecklistAssignment::with(['checklist', 'sede.empresa', 'user'])
            ->when($consultoraId, fn ($q) => $q->whereHas('sede.empresa', fn ($eq) => $eq->where('consultora_id', $consultoraId)))
            ->when($request->status && $request->status !== 'all', fn ($q) => $q->where('status', $request->status))
            ->when($request->sede_id, fn ($q) => $q->where('sede_id', $request->sede_id))
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return [
            'assignments' => $assignments,
            'empresas' => Empresa::with('sedes')
                ->when($consultoraId, fn ($q) => $q->where('consultora_id', $consultoraId))
                ->orderBy('nombre')
                ->get(['id', 'nombre']),
            'checklists' => DynamicChecklist::where('is_active', 1)->orderBy('title')->get(['id', 'title', 'registry_code']),
            'tecnicos' => User::whereIn('rol', ['tecnico', 'technician'])
                ->where('estado', 'activo')
                ->when($consultoraId, fn ($q) => $q->where('consultora_id', $consultoraId))
                ->orderBy('apellidos')
                ->get(['id', 'nombres', 'apellidos']),
            'filters' => $request->only(['status', 'sede_id']),
        ];
    }

    /**
     * Create a checklist assignment for a sede & optional technician.
     */
    public function createAssignment(User $user, array $validated): ChecklistAssignment
    {
        $sede = Sede::with('empresa')->findOrFail($validated['sede_id']);
        if (! $user->isSuperAdmin() && $sede->empresa?->consultora_id !== $user->consultora_id) {
            abort(403, 'No tienes permiso para asignar checklists en esta sede.');
        }

        return ChecklistAssignment::create([
            'dynamic_checklist_id' => $validated['dynamic_checklist_id'],
            'sede_id' => $sede->id,
            'empresa_id' => $sede->empresa_id,
            'user_id' => $validated['user_id'] ?? null,
            'due_date' => $validated['due_date'],
            'frequency' => $validated['frequency'] ?? 'once',
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);
    }

    /**
     * Get the current user's consultora branding payload.
     */
    public function getBrandingData(User $user): ?array
    {
        $consultora = $user->consultora;
        if (! $consultora) {
            return null;
        }

        return [
            'id' => $consultora->id,
            'nombre' => $consultora->nombre,
            'cuit' => $consultora->cuit,
            'email' => $consultora->email,
            'phone' => $consultora->phone,
            'description' => $consultora->description,
            'color_primary' => $consultora->color_primary ?? '#ea580c',
            'color_secondary' => $consultora->color_secondary ?? '#334155',
            'color_accent' => $consultora->color_accent ?? '#10b981',
            'logo_url' => $consultora->logo_path ? asset('storage/'.$consultora->logo_path) : null,
        ];
    }

    /**
     * Update consultora branding colors, contact details, and optional logo.
     */
    public function updateBranding(Consultora $consultora, Request $request, array $validated): void
    {
        $data = collect($validated)->except('logo')->toArray();

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos/consultoras', 'public');
        }

        $consultora->update($data);
    }
}
