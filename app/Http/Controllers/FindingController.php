<?php

namespace App\Http\Controllers;

use App\Models\Hallazgo;
use App\Models\User;
use App\Services\FindingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FindingController extends Controller
{
    public function __construct(protected FindingService $findingService) {}

    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        return Inertia::render('Gestor/Findings/Index', $this->findingService->getIndexData($user, $request));
    }

    public function create(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $visitId = $request->visit_id ? (int) $request->visit_id : null;

        return Inertia::render('Tecnico/Findings/Create', $this->findingService->getCreateData($user, $visitId));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sede_id' => 'required|exists:sedes,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'criticidad' => 'required|in:bajo,medio,alto,critico',
            'plazo_correccion' => 'nullable|date|after_or_equal:today',
            'ubicacion_especifica' => 'nullable|string|max:255',
            'accion_inmediata' => 'nullable|string',
            'visit_id' => 'nullable|exists:visit_certificates,id',
            'fotos.*' => 'nullable|image|max:5120',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $hallazgo = $this->findingService->createFinding($user, $validated, $request->file('fotos', []));

        if ($user->isTechnician() && $hallazgo->visit_certificate_id) {
            return redirect()->route('tecnico.visits.in-progress', $hallazgo->visit_certificate_id)
                ->with('message', 'Hallazgo registrado con éxito.');
        }

        $routePrefix = $user->isTechnician() ? 'tecnico.hallazgos' : 'gestor.hallazgos';

        return redirect()->route($routePrefix.'.show', $hallazgo->id)
            ->with('message', 'Hallazgo registrado y certificado tácticamente.');
    }

    public function show(Hallazgo $finding)
    {
        $finding->load(['sede.empresa', 'user', 'fotos', 'seguimientos.user', 'visit']);

        /** @var User $user */
        $user = Auth::user();
        if (! $user->isSuperAdmin() && $finding->sede?->empresa?->consultora_id !== $user->consultora_id) {
            abort(403, 'No tienes permiso para acceder a este hallazgo.');
        }

        return Inertia::render('Gestor/Findings/Show', ['finding' => $finding]);
    }

    public function addUpdate(Request $request, Hallazgo $finding)
    {
        $request->validate([
            'estado_nuevo' => 'required|string',
            'comentario' => 'required|string|min:5',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $finding->loadMissing('sede.empresa');
        if (! $user->isSuperAdmin() && $finding->sede?->empresa?->consultora_id !== $user->consultora_id) {
            abort(403, 'No tienes permiso para modificar este hallazgo.');
        }

        if ($finding->estado === Hallazgo::ESTADO_CERRADO && $user->isTechnician()) {
            abort(403, 'No se puede modificar un hallazgo que ya está cerrado.');
        }

        if ($user->isTechnician() && in_array($request->estado_nuevo, [Hallazgo::ESTADO_CERRADO, Hallazgo::ESTADO_VERIFICADO])) {
            abort(403, 'Los técnicos no están autorizados a verificar o cerrar hallazgos.');
        }

        $finding->agregarSeguimiento($request->estado_nuevo, $request->comentario, Auth::id());

        return back()->with('message', 'Estado del hallazgo actualizado.');
    }

    public function myFindings(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        return Inertia::render('Gestor/Findings/Index', $this->findingService->getMyFindingsData($user, $request));
    }
}
