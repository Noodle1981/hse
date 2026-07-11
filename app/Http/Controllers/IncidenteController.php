<?php

namespace App\Http\Controllers;

use App\Models\Incidente;
use App\Models\IncidenteFoto;
use App\Models\Sede;
use App\Models\User;
use App\Models\VisitCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class IncidenteController extends Controller
{
    /**
     * Display a listing of incidents for Managers.
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // 1. DATA PARA FILTROS
        $sedes = Sede::orderBy('nombre_sede')->get();

        // 2. QUERY BASE
        $query = Incidente::query()
            ->with(['sede.empresa', 'user'])
            ->latest('fecha_ocurrencia');

        // Tenancy check (if not super admin, only own consultora)
        if (! $user->isSuperAdmin()) {
            $query->whereHas('sede', function ($q) use ($user) {
                $q->where('consultora_id', $user->consultora_id);
            });
        }

        // 4. FILTROS TÁCTICOS
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

        // 5. INTELIGENCIA DE SEGURIDAD (KPIs)
        $kpis = [
            'total' => (clone $query)->count(),
            'accidentes' => (clone $query)->where('tipo', 'accidente')->count(),
            'graves' => (clone $query)->whereIn('gravedad', ['grave', 'fatal'])->count(),
            'dias_perdidos' => (clone $query)->sum('dias_perdidos') ?: 0,
            'afectados' => (clone $query)->sum('personas_afectadas') ?: 0,
        ];

        return Inertia::render('Gestor/Incidents/Index', [
            'incidents' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'tipo', 'gravedad', 'sede_id']),
            'kpis' => $kpis,
            'sedes' => $sedes,
        ]);
    }

    /**
     * Show form to report a new incident.
     */
    public function create(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $sedes = $user->isTechnician()
            ? $user->sedes()->with('empresa')->get()
            : Sede::with('empresa')->orderBy('nombre_sede')->get();

        $initialSedeId = null;
        if ($request->visit_id) {
            $visit = VisitCertificate::find($request->visit_id);
            if ($visit) {
                $initialSedeId = $visit->sede_id;
            }
        }

        return Inertia::render('Tecnico/Incidents/Create', [
            'sedes' => $sedes,
            'visit_id' => $request->visit_id,
            'initial_sede_id' => $initialSedeId,
        ]);
    }

    /**
     * Store a newly created incident.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|in:accidente,incidente,cuasi_accidente',
            'gravedad' => 'required|in:leve,moderado,grave,fatal',
            'fecha_ocurrencia' => 'required|date|before_or_equal:today',
            'sede_id' => 'required|exists:sedes,id',
            'descripcion' => 'required|string',
            'medidas_tomadas' => 'required|string',
            'personas_afectadas' => 'required|integer|min:0',
            'dias_perdidos' => 'nullable|integer|min:0',
            'fotos.*' => 'nullable|image|max:5120',
            'visit_certificate_id' => 'nullable|exists:visit_certificates,id',
        ]);

        return DB::transaction(function () use ($request) {
            $incidente = Incidente::create([
                ...$request->except('fotos'),
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

            /** @var User $user */
            $user = Auth::user();

            if ($user->isTechnician()) {
                $redirect = $request->visit_certificate_id
                    ? route('tecnico.visits.in-progress', $request->visit_certificate_id)
                    : route('tecnico.checklists.tasks.index');
            } else {
                $redirect = route('gestor.incidentes.index');
            }

            return redirect($redirect)->with('message', 'Incidente reportado exitosamente.');
        });
    }

    /**
     * Display detailed incident analysis.
     */
    public function show(Incidente $incident)
    {
        $incident->load(['sede.empresa', 'user', 'fotos', 'hallazgo', 'visit']);

        return Inertia::render('Gestor/Incidents/Show', [
            'incident' => $incident,
        ]);
    }
}
