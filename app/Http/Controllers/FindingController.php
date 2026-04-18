<?php

namespace App\Http\Controllers;

use App\Models\Hallazgo;
use App\Models\HallazgoFoto;
use App\Models\HallazgoSeguimiento;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class FindingController extends Controller
{
    /**
     * Display a listing of findings.
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $query = Hallazgo::query()
            ->with(['sede.empresa', 'user'])
            ->latest();

        // Si es técnico, solo sus hallazgos o hallazgos en sus sedes
        if ($user->isTechnician()) {
            $query->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereIn('sede_id', $user->sedes->pluck('id'));
            });
        }

        // Filtros
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('titulo', 'like', "%{$request->search}%")
                  ->orWhereHas('sede', function($sq) use ($request) {
                      $sq->where('nombre_sede', 'like', "%{$request->search}%");
                  });
            });
        }

        if ($request->status && $request->status !== 'all') {
            $query->where('estado', $request->status);
        }

        if ($request->criticidad && $request->criticidad !== 'all') {
            $query->where('criticidad', $request->criticidad);
        }

        return Inertia::render('Gestor/Findings/Index', [
            'findings' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'status', 'criticidad']),
            'stats' => [
                'total' => $query->count(),
                'pendientes' => (clone $query)->where('estado', 'pendiente')->count(),
                'criticos' => (clone $query)->where('criticidad', 'critico')->count(),
            ]
        ]);
    }

    /**
     * Show the form for creating a new finding.
     */
    public function create(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $sedes = $user->isTechnician() 
            ? $user->sedes()->with('empresa')->get()
            : Sede::with('empresa')->orderBy('nombre_sede')->get();

        return Inertia::render('Tecnico/Findings/Create', [
            'sedes' => $sedes,
            'visit_id' => $request->visit_id, // Contexto opcional de visita
        ]);
    }

    /**
     * Store a newly created finding.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'sede_id' => 'required|exists:sedes,id',
            'criticidad' => 'required|in:bajo,medio,alto,critico',
            'plazo_correccion' => 'required|date|after_or_equal:today',
            'fotos.*' => 'nullable|image|max:5120', // 5MB max por foto
        ]);

        return DB::transaction(function () use ($request) {
            $hallazgo = Hallazgo::create([
                ...$request->except('fotos'),
                'user_id' => Auth::id(),
                'estado' => 'pendiente',
                'fecha_deteccion' => now(),
            ]);

            // Manejo de Fotos
            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $index => $file) {
                    $path = $file->store('hallazgos/' . $hallazgo->id, 'public');
                    HallazgoFoto::create([
                        'hallazgo_id' => $hallazgo->id,
                        'tipo' => HallazgoFoto::TIPO_ANTES,
                        'ruta_archivo' => $path,
                        'descripcion' => 'Foto de evidencia inicial #' . ($index + 1),
                    ]);
                }
            }

            // Registro automático de seguimiento inicial
            $hallazgo->seguimientos()->create([
                'user_id' => Auth::id(),
                'estado_anterior' => 'nuevo',
                'estado_nuevo' => 'pendiente',
                'comentario' => 'Hallazgo registrado en el sistema.',
                'fecha_seguimiento' => now(),
            ]);

            $routePrefix = Auth::user()->isTechnician() ? 'tecnico.hallazgos' : 'gestor.hallazgos';
            return redirect()->route($routePrefix . '.show', $hallazgo->id)
                ->with('message', 'Hallazgo registrado y certificado táctocamente.');
        });
    }

    /**
     * Display the specified finding.
     */
    public function show(Hallazgo $finding)
    {
        $finding->load(['sede.empresa', 'user', 'fotos', 'seguimientos.user', 'visit']);

        return Inertia::render('Gestor/Findings/Show', [
            'finding' => $finding,
        ]);
    }

    /**
     * Add a follow-up action or change status.
     */
    public function addUpdate(Request $request, Hallazgo $finding)
    {
        $request->validate([
            'estado_nuevo' => 'required|string',
            'comentario' => 'required|string|min:5',
        ]);

        $finding->agregarSeguimiento(
            $request->estado_nuevo,
            $request->comentario,
            Auth::id()
        );

        return back()->with('message', 'Estado del hallazgo actualizado.');
    }

    /**
     * Mis Hallazgos (Vista para el Técnico).
     */
    public function myFindings(Request $request)
    {
        $user = Auth::user();
        $query = Hallazgo::query()
            ->with(['sede.empresa', 'user'])
            ->where('user_id', $user->id)
            ->latest();

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('titulo', 'like', "%{$request->search}%")
                  ->orWhereHas('sede', fn($sq) => $sq->where('nombre_sede', 'like', "%{$request->search}%"));
            });
        }

        return Inertia::render('Gestor/Findings/Index', [
            'findings' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search']),
            'stats' => [
                'total' => $query->count(),
                'pendientes' => (clone $query)->where('estado', 'pendiente')->count(),
                'criticos' => (clone $query)->where('criticidad', 'critico')->count(),
            ],
            'isTecnico' => true,
        ]);
    }
}
