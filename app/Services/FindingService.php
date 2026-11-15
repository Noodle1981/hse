<?php

namespace App\Services;

use App\Models\Hallazgo;
use App\Models\HallazgoFoto;
use App\Models\Sede;
use App\Models\User;
use App\Models\VisitCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FindingService
{
    public function getIndexData(User $user, Request $request): array
    {
        $sedesQuery = Sede::orderBy('nombre_sede');
        $tecnicosQuery = User::whereIn('rol', ['tecnico', 'Técnico'])->orderBy('nombres');

        if (! $user->isSuperAdmin()) {
            $sedesQuery->whereHas('empresa', fn ($q) => $q->where('consultora_id', $user->consultora_id));
            $tecnicosQuery->where('consultora_id', $user->consultora_id);
        }

        $query = Hallazgo::query()
            ->with(['sede.empresa', 'user'])
            ->withCount(['fotos', 'seguimientos'])
            ->latest();

        if (! $user->isSuperAdmin()) {
            $query->whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $user->consultora_id));
        }

        if ($user->isTechnician()) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereIn('sede_id', $user->sedes->pluck('id'));
            });
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('titulo', 'like', "%{$request->search}%")
                    ->orWhere('descripcion', 'like', "%{$request->search}%")
                    ->orWhere('ubicacion_especifica', 'like', "%{$request->search}%");
            });
        }

        if ($request->status && $request->status !== 'all') {
            $query->where('estado', $request->status);
        }

        if ($request->criticidad && $request->criticidad !== 'all') {
            $query->where('criticidad', $request->criticidad);
        }

        if ($request->sede_id && $request->sede_id !== 'all') {
            $query->where('sede_id', $request->sede_id);
        }

        if ($request->user_id && $request->user_id !== 'all') {
            $query->where('user_id', $request->user_id);
        }

        if ($request->vencidos === 'si') {
            $query->vencidos();
        } elseif ($request->vencidos === 'no') {
            $query->where(function ($q) {
                $q->where('plazo_correccion', '>=', now())
                    ->orWhereIn('estado', ['resuelto', 'cerrado']);
            });
        }

        $kpiBase = Hallazgo::query();
        if (! $user->isSuperAdmin()) {
            $kpiBase->whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $user->consultora_id));
        }

        return [
            'findings' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'status', 'criticidad', 'sede_id', 'user_id', 'vencidos']),
            'kpis' => [
                'total' => (clone $query)->count(),
                'pendientes' => (clone $kpiBase)->pendientes()->count(),
                'criticos' => (clone $kpiBase)->criticos()->abiertos()->count(),
                'vencidos' => (clone $kpiBase)->vencidos()->count(),
                'resueltos_mes' => (clone $kpiBase)->whereIn('estado', ['resuelto', 'cerrado'])
                    ->where('updated_at', '>=', now()->startOfMonth())
                    ->count(),
            ],
            'sedes' => $sedesQuery->get(),
            'tecnicos' => $tecnicosQuery->get(),
        ];
    }

    public function getCreateData(User $user, ?int $visitId): array
    {
        $sedes = $user->isTechnician()
            ? $user->sedes()->with('empresa')->get()
            : Sede::with('empresa')
                ->when(! $user->isSuperAdmin(), fn ($q) => $q->whereHas('empresa', fn ($eq) => $eq->where('consultora_id', $user->consultora_id)))
                ->orderBy('nombre_sede')
                ->get();

        $initialSedeId = $visitId ? VisitCertificate::find($visitId)?->sede_id : null;

        return [
            'sedes' => $sedes,
            'visit_id' => $visitId,
            'initial_sede_id' => $initialSedeId,
        ];
    }

    public function createFinding(User $user, array $validated, array $fotos = []): Hallazgo
    {
        return DB::transaction(function () use ($user, $validated, $fotos) {
            $hallazgo = Hallazgo::create([
                'sede_id' => $validated['sede_id'],
                'user_id' => $user->id,
                'visit_certificate_id' => $validated['visit_id'] ?? null,
                'titulo' => $validated['titulo'],
                'descripcion' => $validated['descripcion'],
                'ubicacion_especifica' => $validated['ubicacion_especifica'] ?? null,
                'criticidad' => $validated['criticidad'],
                'plazo_correccion' => $validated['plazo_correccion'] ?? null,
                'accion_inmediata' => $validated['accion_inmediata'] ?? null,
                'estado' => Hallazgo::ESTADO_PENDIENTE,
                'origen_type' => 'manual',
            ]);

            foreach ($fotos as $file) {
                $path = $file->store('hallazgos/'.$hallazgo->id, 'public');
                HallazgoFoto::create([
                    'hallazgo_id' => $hallazgo->id,
                    'ruta_archivo' => $path,
                    'descripcion' => 'Evidencia inicial de campo',
                ]);
            }

            $hallazgo->agregarSeguimiento(
                Hallazgo::ESTADO_PENDIENTE,
                'Hallazgo reportado inicialmente.',
                $user->id
            );

            return $hallazgo;
        });
    }

    public function getMyFindingsData(User $user, Request $request): array
    {
        $query = Hallazgo::query()
            ->with(['sede.empresa', 'user'])
            ->where('user_id', $user->id)
            ->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('titulo', 'like', "%{$request->search}%")
                    ->orWhereHas('sede', fn ($sq) => $sq->where('nombre_sede', 'like', "%{$request->search}%"));
            });
        }

        return [
            'findings' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search']),
            'kpis' => [
                'total' => (clone $query)->count(),
                'pendientes' => (clone $query)->where('estado', 'pendiente')->count(),
                'criticos' => (clone $query)->where('criticidad', 'critico')->count(),
                'vencidos' => (clone $query)->vencidos()->count(),
                'resueltos_mes' => (clone $query)->whereIn('estado', ['resuelto', 'cerrado'])
                    ->where('updated_at', '>=', now()->startOfMonth())
                    ->count(),
            ],
            'sedes' => $user->sedes()->with('empresa')->get(),
            'tecnicos' => [$user],
            'isTecnico' => true,
        ];
    }
}
