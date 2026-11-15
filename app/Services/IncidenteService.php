<?php

namespace App\Services;

use App\Models\Incidente;
use App\Models\IncidenteFoto;
use App\Models\Sede;
use App\Models\User;
use App\Models\VisitCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IncidenteService
{
    public function getIndexData(User $user, Request $request): array
    {
        $sedes = Sede::orderBy('nombre_sede')
            ->when(! $user->isSuperAdmin(), fn ($q) => $q->whereHas('empresa', fn ($eq) => $eq->where('consultora_id', $user->consultora_id)))
            ->get();

        $query = Incidente::query()
            ->with(['sede.empresa', 'user'])
            ->latest('fecha_ocurrencia');

        if (! $user->isSuperAdmin()) {
            $query->whereHas('sede.empresa', function ($q) use ($user) {
                $q->where('consultora_id', $user->consultora_id);
            });
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('titulo', 'like', "%{$request->search}%")
                    ->orWhere('descripcion', 'like', "%{$request->search}%")
                    ->orWhereHas('sede', function ($sq) use ($request) {
                        $sq->where('nombre_sede', 'like', "%{$request->search}%");
                    });
            });
        }

        if ($request->tipo && $request->tipo !== 'all') {
            $query->where('tipo', $request->tipo);
        }

        if ($request->gravedad && $request->gravedad !== 'all') {
            $query->where('gravedad', $request->gravedad);
        }

        if ($request->sede_id && $request->sede_id !== 'all') {
            $query->where('sede_id', $request->sede_id);
        }

        $kpis = [
            'total' => (clone $query)->count(),
            'accidentes' => (clone $query)->where('tipo', 'accidente')->count(),
            'graves' => (clone $query)->whereIn('gravedad', ['grave', 'fatal'])->count(),
            'dias_perdidos' => (clone $query)->sum('dias_perdidos') ?: 0,
            'afectados' => (clone $query)->sum('personas_afectadas') ?: 0,
        ];

        return [
            'incidents' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'tipo', 'gravedad', 'sede_id']),
            'kpis' => $kpis,
            'sedes' => $sedes,
        ];
    }

    public function getCreateData(User $user, $visitId = null): array
    {
        $sedes = $user->isTechnician()
            ? $user->sedes()->with('empresa')->get()
            : Sede::with('empresa')
                ->when(! $user->isSuperAdmin(), fn ($q) => $q->whereHas('empresa', fn ($eq) => $eq->where('consultora_id', $user->consultora_id)))
                ->orderBy('nombre_sede')
                ->get();

        $initialSedeId = null;
        if ($visitId) {
            $visit = VisitCertificate::find($visitId);
            if ($visit) {
                $initialSedeId = $visit->sede_id;
            }
        }

        return [
            'sedes' => $sedes,
            'visit_id' => $visitId,
            'initial_sede_id' => $initialSedeId,
        ];
    }

    public function createIncidente(Request $request, array $validated): Incidente
    {
        return DB::transaction(function () use ($request, $validated) {
            unset($validated['fotos']);
            $incidente = Incidente::create([
                ...$validated,
                'user_id' => Auth::id(),
                'estado' => 'reportado',
            ]);

            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $file) {
                    $path = $file->store('incidentes/'.$incidente->id, 'public');
                    IncidenteFoto::create([
                        'incidente_id' => $incidente->id,
                        'ruta_archivo' => $path,
                        'descripcion' => 'Evidencia de incidente',
                    ]);
                }
            }

            return $incidente;
        });
    }
}
