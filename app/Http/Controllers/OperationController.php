<?php

namespace App\Http\Controllers;

use App\Models\DynamicInspection;
use App\Models\VisitCertificate;
use App\Services\OperationService;
use App\Services\TechnicianAssignmentService;
use App\Services\VisitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class OperationController extends Controller
{
    public function __construct(
        protected VisitService $visitService,
        private readonly OperationService $operationService,
        private readonly TechnicianAssignmentService $assignmentService
    ) {}

    public function index(Request $request)
    {
        return Inertia::render('Gestor/Visits/Index', $this->operationService->getVisitsIndexData(Auth::user(), $request));
    }

    public function myAssignments(Request $request)
    {
        return Inertia::render('Tecnico/MyAssignments', $this->assignmentService->getMyAssignmentsData(Auth::user(), $request));
    }

    public function startQuickExecution(Request $request)
    {
        $request->validate([
            'type' => 'required|in:inspection,training',
            'id' => 'required_without:title',
            'source' => 'required|in:manual,contract,adhoc',
            'sede_id' => 'required|exists:sedes,id',
            'title' => 'nullable|string|max:255',
            'duration' => 'nullable',
            'description' => 'nullable|string',
            'modality' => 'nullable|string|in:presencial,virtual',
            'checkin_lat' => 'nullable|numeric',
            'checkin_lng' => 'nullable|numeric',
        ]);

        $result = $this->operationService->startQuickExecution(Auth::user(), $request);

        return isset($result['error'])
            ? back()->with('error', $result['error'])
            : redirect($result['redirect'])->with('message', $result['message']);
    }

    public function create()
    {
        return Inertia::render('Gestor/Visits/Start', ['sedes' => $this->operationService->getCreateVisitSedes(Auth::user())]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'sede_id' => 'required|exists:sedes,id',
            'checkin_lat' => 'nullable|numeric',
            'checkin_lng' => 'nullable|numeric',
        ]);

        $visit = $this->operationService->startVisitForSede(
            Auth::user(),
            $request->sede_id,
            $request->filled('checkin_lat') ? (float) $request->checkin_lat : null,
            $request->filled('checkin_lng') ? (float) $request->checkin_lng : null
        );

        return redirect()->route('gestor.operations.visits.show', $visit->id)
            ->with('message', 'Visita iniciada. Sincronizando zona de despliegue...');
    }

    public function show(VisitCertificate $visit)
    {
        $result = $this->operationService->prepareShowVisit(Auth::user(), $visit);

        return isset($result['redirect'])
            ? redirect($result['redirect'])->with('info', $result['info'])
            : Inertia::render('Tecnico/Visits/InProgress', $result['props']);
    }

    public function inspections(Request $request)
    {
        return Inertia::render('Gestor/Inspections/Index', $this->operationService->getCompletedInspections($request));
    }

    public function showInspection(DynamicInspection $inspection)
    {
        $inspection->load(['checklist.sections.questions', 'answers', 'sede.empresa', 'user']);

        return Inertia::render('Gestor/Inspections/Show', ['inspection' => $inspection]);
    }

    public function myVisits(Request $request)
    {
        return Inertia::render('Tecnico/Visits/History', $this->operationService->getMyVisitsData(Auth::user(), $request));
    }

    public function finalize(Request $request, VisitCertificate $visit)
    {
        try {
            $request->validate([
                'notes' => 'nullable|string',
                'responsible_name' => 'required|string',
                'signature' => 'required_without:signature_skip_reason|nullable|string',
                'signature_skip_reason' => 'required_if:signature_unavailable,true|nullable|string',
                'checkout_lat' => 'nullable|numeric',
                'checkout_lng' => 'nullable|numeric',
            ]);
        } catch (ValidationException $e) {
            Log::warning('Fallo de validación al finalizar visita', ['errors' => $e->errors()]);
            throw $e;
        }

        $this->visitService->finalizeVisit(
            $visit,
            $request->notes,
            $request->signature,
            $request->responsible_name,
            $request->email_responsable,
            $request->signature_skip_reason,
            $request->filled('checkout_lat') ? (float) $request->checkout_lat : null,
            $request->filled('checkout_lng') ? (float) $request->checkout_lng : null
        );

        $redirectRoute = str_contains($request->route()->getName(), 'tecnico')
            ? 'tecnico.visits.index'
            : 'gestor.operations.visits.index';

        return redirect()->route($redirectRoute)->with('message', '¡Visita finalizada y certificada exitosamente!');
    }

    public function checkout(Request $request, VisitCertificate $visit)
    {
        $request->validate(['checkout_lat' => 'nullable|numeric', 'checkout_lng' => 'nullable|numeric']);
        $visit = $this->operationService->checkoutVisit(
            $visit,
            $request->filled('checkout_lat') ? (float) $request->checkout_lat : null,
            $request->filled('checkout_lng') ? (float) $request->checkout_lng : null
        );

        return response()->json([
            'success' => true,
            'gps_verified' => $visit->gps_verified,
            'message' => 'Geolocalización de salida procesada correctamente.',
        ]);
    }

    public function assignSuggestedTask(Request $request, VisitCertificate $visit)
    {
        $request->validate(['plan_id' => 'required|exists:annual_plans,id']);
        $result = $this->operationService->assignSuggestedTask($visit, $request->plan_id);

        return redirect($result['redirect'])->with('message', $result['message']);
    }
}
