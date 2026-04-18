<?php

namespace App\Http\Controllers;

use App\Models\ChecklistAssignment;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\User;
use App\Models\VisitCertificate;
use App\Services\VisitService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OperationController extends Controller
{
    protected $visitService;

    public function __construct(VisitService $visitService)
    {
        $this->visitService = $visitService;
    }

    /**
     * Display a listing of visits.
     */
    public function index(Request $request)
    {
        $query = VisitCertificate::query()
            ->with(['sede.empresa', 'user'])
            ->latest('visit_date');

        // Filtros básicos
        if ($request->search) {
            $query->whereHas('sede', function ($q) use ($request) {
                $q->where('nombre_sede', 'like', "%{$request->search}%");
            });
        }

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        return Inertia::render('Gestor/Visits/Index', [
            'visits' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'status']),
            'activeVisit' => $this->visitService->getActiveVisit(Auth::user()),
        ]);
    }

    /**
     * Display the Technician's dashboard (My Assignments).
     */
    public function myAssignments(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $now = Carbon::now();
        $selectedMonth = $request->month ?? $now->month;
        $search = $request->search;
        $selectedEmpresa = $request->empresa_id;
        $selectedSede = $request->sede_id;
        $activeTab = $request->tab ?? 'inspections';

        // IDs de Sedes y Empresas ASIGNADAS al técnico
        $assignedSedes = $user->sedes()->select('sedes.id', 'sedes.empresa_id')->get();
        $assignedSedeIds = $assignedSedes->pluck('id')->toArray();
        $assignedEmpresaIds = $assignedSedes->pluck('empresa_id')->unique()->toArray();

        $activeVisit = $this->visitService->getActiveVisit($user);

        // 1. Manual Assignments (Checklists)
        $manualQuery = ChecklistAssignment::with(['checklist.categoryRelation', 'checklist.questions', 'sede.empresa'])
            ->where('user_id', $user->id)
            ->whereIn('sede_id', $assignedSedeIds)
            ->whereMonth('due_date', $selectedMonth)
            ->whereYear('due_date', $now->year)
            ->where(function($q) {
                $q->whereNull('notes')
                  ->orWhere(function($sq) {
                      $sq->where('notes', 'NOT LIKE', '%Vínculo automático desde Plan Anual%')
                         ->where('notes', 'NOT LIKE', '%Plan Mensual Recomendado%');
                  });
            });

        // 2. Trainings
        $trainingsQuery = \App\Models\Training::with(['sede.empresa', 'dynamicChecklist.categoryRelation'])
            ->where('user_id', $user->id)
            ->whereIn('sede_id', $assignedSedeIds)
            ->whereMonth('date', $selectedMonth)
            ->whereYear('date', $now->year);

        // 3. Reports
        $reportsQuery = \App\Models\MasterReport::with(['sede.empresa'])
            ->where('user_id', $user->id)
            ->whereIn('sede_id', $assignedSedeIds)
            ->whereMonth('created_at', $selectedMonth)
            ->whereYear('created_at', $now->year);

        // 4. Annual Plan Tasks
        $queryMonth = $now->year . '-' . str_pad($selectedMonth, 2, '0', STR_PAD_LEFT);
        $contractQuery = \App\Models\AnnualPlan::with(['checklist.categoryRelation', 'sede.empresa'])
            ->whereIn('sede_id', $assignedSedeIds)
            ->where('scheduled_months', 'like', '%"' . $queryMonth . '"%');

        // Apply filters (Empresa/Sede/Search)
        if ($selectedEmpresa) {
            $manualQuery->whereHas('sede', fn($q) => $q->where('empresa_id', $selectedEmpresa));
            $trainingsQuery->whereHas('sede', fn($q) => $q->where('empresa_id', $selectedEmpresa));
            $reportsQuery->whereHas('sede', fn($q) => $q->where('empresa_id', $selectedEmpresa));
            $contractQuery->whereHas('sede', fn($q) => $q->where('empresa_id', $selectedEmpresa));
        }
        if ($selectedSede) {
            $manualQuery->where('sede_id', $selectedSede);
            $trainingsQuery->where('sede_id', $selectedSede);
            $reportsQuery->where('sede_id', $selectedSede);
            $contractQuery->where('sede_id', $selectedSede);
        }
        if ($search) {
            $manualQuery->whereHas('checklist', fn($q) => $q->where('title', 'like', "%{$search}%"));
            $trainingsQuery->where('title', 'like', "%{$search}%");
            $reportsQuery->where('titulo', 'like', "%{$search}%");
            $contractQuery->where('activity_name', 'like', "%{$search}%");
        }

        // Processing results for unified view (simplified version of legacy logic)
        $inspections = $manualQuery->get()->map(fn($a) => [
            'id' => $a->id,
            'type' => 'inspection',
            'title' => $a->checklist?->title,
            'sede' => $a->sede?->nombre_sede,
            'empresa' => $a->sede?->empresa?->nombre,
            'due_date' => $a->due_date,
            'status' => $a->status,
            'checklist_id' => $a->dynamic_checklist_id,
        ])->concat($contractQuery->where('category', 'Inspección')->get()->map(fn($p) => [
            'id' => $p->id,
            'type' => 'inspection',
            'title' => $p->checklist?->title ?? $p->activity_name,
            'sede' => $p->sede?->nombre_sede,
            'empresa' => $p->sede?->empresa?->nombre,
            'due_date' => Carbon::parse($queryMonth . '-01')->endOfMonth(),
            'status' => 'scheduled', // Simplified status mapping
            'checklist_id' => $p->checklist_id,
        ]));

        $trainings = $trainingsQuery->get()->map(fn($t) => [
            'id' => $t->id,
            'type' => 'training',
            'title' => $t->title,
            'sede' => $t->sede?->nombre_sede,
            'empresa' => $t->sede?->empresa?->nombre,
            'due_date' => $t->date,
            'status' => $t->status,
        ]);

        $reports = $reportsQuery->get()->map(fn($r) => [
            'id' => $r->id,
            'type' => 'report',
            'title' => $r->titulo,
            'sede' => $r->sede?->nombre_sede,
            'empresa' => $r->sede?->empresa?->nombre,
            'due_date' => $r->created_at->addDays(7),
            'status' => $r->estado,
        ]);

        // KPIs
        $kpis = [
            'total_tasks' => $inspections->count() + $trainings->count() + $reports->count(),
            'completed' => $inspections->where('status', 'completed')->count() + $trainings->where('status', 'completed')->count(),
            'critical_findings' => \App\Models\Hallazgo::where('inspector_id', $user->id)->where('criticidad', 'alta')->where('estado', '!=', 'resuelto')->count(),
        ];

        return Inertia::render('Tecnico/MyAssignments', [
            'inspections' => $inspections,
            'trainings' => $trainings,
            'reports' => $reports,
            'kpis' => $kpis,
            'activeVisit' => $activeVisit,
            'filters' => [
                'month' => (int)$selectedMonth,
                'search' => $search,
                'empresa_id' => $selectedEmpresa,
                'sede_id' => $selectedSede,
                'tab' => $activeTab
            ],
            'empresas' => Empresa::whereIn('id', $assignedEmpresaIds)->get(),
            'sedes' => Sede::whereIn('id', $assignedSedeIds)->get(),
        ]);
    }

    /**
     * Show modal/page to start a new visit.
     */
    public function create()
    {
        $sedes = Sede::with('empresa')->orderBy('nombre_sede')->get();
        
        return Inertia::render('Gestor/Visits/Start', [
            'sedes' => $sedes,
        ]);
    }

    /**
     * Start a visit.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sede_id' => 'required|exists:sedes,id',
        ]);

        $sede = Sede::findOrFail($request->sede_id);
        
        // El servicio maneja la lógica de si ya hay una activa o no
        $visit = $this->visitService->startVisit(Auth::user(), $sede);

        return redirect()->route('gestor.operations.visits.show', $visit->id)
            ->with('message', 'Visita iniciada. Sincronizando zona de despliegue...');
    }

    /**
     * The dynamic visit console (In Progress).
     */
    public function show(VisitCertificate $visit)
    {
        /** @var User $user */
        $user = Auth::user();

        // Seguridad: Solo el técnico asignado o un admin
        if ($visit->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        if ($visit->status === 'completed' || $visit->status === 'signed') {
            return redirect()->route('gestor.operations.visits.index')
                ->with('info', 'Esta visita ya ha sido finalizada.');
        }

        // Cargar contexto completo
        $visit->load([
            'sede.empresa',
            'checklistCompletions.assignment.checklist',
            'trainings.training',
            'hallazgos',
            'incidentes'
        ]);

        return Inertia::render('Tecnico/Visits/InProgress', [
            'visit' => $visit,
            'user' => $user,
            'templates' => \App\Models\Training::where('is_template', true)->get(),
        ]);
    }

    /**
     * Display a listing of completed inspections (History).
     */
    public function inspections(Request $request)
    {
        $query = \App\Models\DynamicInspection::query()
            ->with(['checklist', 'sede', 'user'])
            ->where('status', 'completed')
            ->latest('completed_at');

        if ($request->search) {
            $query->whereHas('sede', function ($q) use ($request) {
                $q->where('nombre_sede', 'like', "%{$request->search}%");
            })->orWhereHas('checklist', function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%");
            });
        }

        return Inertia::render('Gestor/Inspections/Index', [
            'inspections' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show a detailed view of a single inspection.
     */
    public function showInspection(\App\Models\DynamicInspection $inspection)
    {
        $inspection->load(['checklist.sections.questions', 'answers', 'sede.empresa', 'user']);
        
        return Inertia::render('Gestor/Inspections/Show', [
            'inspection' => $inspection,
        ]);
    }

    /**
     * Display a listing of technician's visits (History).
     */
    public function myVisits(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $query = VisitCertificate::query()
            ->with(['sede.empresa'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['completed', 'signed'])
            ->latest('visit_date');

        if ($request->search) {
            $query->whereHas('sede', function ($q) use ($request) {
                $q->where('nombre_sede', 'like', "%{$request->search}%");
            });
        }

        return Inertia::render('Tecnico/Visits/History', [
            'visits' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Finalize the visit.
     */
    public function finalize(Request $request, VisitCertificate $visit)
    {
        $request->validate([
            'notes' => 'required|string|min:10',
            'responsible_name' => 'required|string|min:3',
            'signature' => 'required_without:signature_skip_reason|string',
            'signature_skip_reason' => 'required_if:signature_unavailable,true|string',
        ]);

        $visit->update([
            'status' => 'completed',
            'end_time' => now(),
            'general_observations' => $request->notes,
            'responsible_name' => $request->responsible_name,
            'signature' => $request->signature,
            'signature_skip_reason' => $request->signature_skip_reason,
            'email_responsable' => $request->email_responsable,
        ]);

        return redirect()->route('gestor.operations.visits.index')
            ->with('message', '¡Visita finalizada y certificada exitosamente!');
    }
}
