<?php

namespace App\Http\Controllers;

use App\Models\Incidente;
use App\Models\User;
use App\Services\IncidenteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class IncidenteController extends Controller
{
    public function __construct(protected IncidenteService $incidenteService) {}

    /**
     * Display a listing of incidents for Managers.
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        return Inertia::render('Gestor/Incidents/Index', $this->incidenteService->getIndexData($user, $request));
    }

    /**
     * Show form to report a new incident.
     */
    public function create(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        return Inertia::render('Tecnico/Incidents/Create', $this->incidenteService->getCreateData($user, $request->visit_id));
    }

    /**
     * Store a newly created incident.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
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
            'asset_id' => 'nullable|exists:assets,id',
        ]);

        $this->incidenteService->createIncidente($request, $validated);

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
    }

    /**
     * Display detailed incident analysis.
     */
    public function show(Incidente $incident)
    {
        $incident->load(['sede.empresa', 'user', 'fotos', 'hallazgo', 'visit']);

        /** @var User $user */
        $user = Auth::user();
        if (! $user->isSuperAdmin() && $incident->sede?->empresa?->consultora_id !== $user->consultora_id) {
            abort(403, 'No tienes permiso para acceder a este incidente.');
        }

        return Inertia::render('Gestor/Incidents/Show', [
            'incident' => $incident,
        ]);
    }
}
