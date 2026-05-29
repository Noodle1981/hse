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
use Illuminate\Support\Facades\Log;
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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $now = Carbon::now();
        $selectedMonth = $request->month ?? $now->month;
        $search = $request->search;
        $selectedEmpresa = $request->empresa_id;
        $selectedSede = $request->sede_id;
        $activeTab = $request->tab ?? 'inspections';
        $selectedStatus = $request->status ?? 'all';

        // IDs de Sedes y Empresas ASIGNADAS al técnico
        $assignedSedes = $user->sedes()->select('sedes.id', 'sedes.empresa_id')->get();
        $assignedSedeIds = $assignedSedes->pluck('id')->toArray();
        $assignedEmpresaIds = $assignedSedes->pluck('empresa_id')->unique()->toArray();

        $activeVisit = $this->visitService->getActiveVisit($user);

        // 1️⃣ Manual / Automatic Assignments (Checklists) - LOAD ALL FOR GROUPING
        $assignmentsQuery = ChecklistAssignment::with(['checklist.categoryRelation', 'checklist.questions', 'sede.empresa'])
            ->where('user_id', $user->id)
            ->whereIn('sede_id', $assignedSedeIds)
            ->whereMonth('due_date', $selectedMonth)
            ->whereYear('due_date', $now->year);

        // 2️⃣ Trainings
        $trainingsQuery = \App\Models\Training::with(['sede.empresa', 'dynamicChecklist.categoryRelation'])
            ->where('user_id', $user->id)
            ->whereIn('sede_id', $assignedSedeIds)
            ->whereMonth('date', $selectedMonth)
            ->whereYear('date', $now->year);

        // 3️⃣ Reports
        $reportsQuery = \App\Models\MasterReport::with(['sede.empresa'])
            ->where('user_id', $user->id)
            ->whereIn('sede_id', $assignedSedeIds)
            ->whereMonth('created_at', $selectedMonth)
            ->whereYear('created_at', $now->year);

        // 4️⃣ Annual Plan Tasks
        $queryMonth = $now->year . '-' . str_pad($selectedMonth, 2, '0', STR_PAD_LEFT);
        $contractQuery = \App\Models\AnnualPlan::with(['checklist.categoryRelation', 'sede.empresa'])
            ->whereIn('sede_id', $assignedSedeIds)
            ->whereJsonContains('scheduled_months', $queryMonth);

        // Apply filters (Empresa/Sede/Search)
        if ($selectedEmpresa) {
            $assignmentsQuery->whereHas('sede', fn($q) => $q->where('empresa_id', $selectedEmpresa));
            $trainingsQuery->whereHas('sede', fn($q) => $q->where('empresa_id', $selectedEmpresa));
            $reportsQuery->whereHas('sede', fn($q) => $q->where('empresa_id', $selectedEmpresa));
            $contractQuery->whereHas('sede', fn($q) => $q->where('empresa_id', $selectedEmpresa));
        }
        if ($selectedSede) {
            $assignmentsQuery->where('sede_id', $selectedSede);
            $trainingsQuery->where('sede_id', $selectedSede);
            $reportsQuery->where('sede_id', $selectedSede);
            $contractQuery->where('sede_id', $selectedSede);
        }
        if ($search) {
            $assignmentsQuery->whereHas('checklist', fn($q) => $q->where('title', 'like', "%{$search}%"));
            $trainingsQuery->where('title', 'like', "%{$search}%");
            $reportsQuery->where('titulo', 'like', "%{$search}%");
            $contractQuery->where('activity_name', 'like', "%{$search}%");
        }

        $allAssignments = $assignmentsQuery->orderBy('due_date', 'asc')->get();
        $allTrainings = $trainingsQuery->orderBy('date', 'asc')->get();
        $allReports = $reportsQuery->orderBy('created_at', 'asc')->get();
        $contractTasks = $contractQuery->get();

        // 5️⃣ Procesamiento de resultados para vista unificada con agrupación para evitar duplicados y mantener registro único
        
        // Fetch all completed inspections for this month to calculate the KPI stats
        $completedInspectionsAll = \App\Models\DynamicInspection::whereIn('sede_id', $assignedSedeIds)
            ->whereMonth('completed_at', $selectedMonth)
            ->whereYear('completed_at', $now->year)
            ->where('status', 'completed')
            ->get();

        // 5.1️⃣ Inspecciones
        $unifiedInspections = collect();
        $contractInspections = $contractTasks->where('category', 'Inspección');

        foreach ($contractInspections as $plan) {
            $matchingAssignments = $allAssignments->where('dynamic_checklist_id', $plan->checklist_id)
                ->where('sede_id', $plan->sede_id);

            $status = 'scheduled';
            if ($matchingAssignments->isNotEmpty()) {
                if ($matchingAssignments->whereIn('status', ['completed', 'finalizado'])->isNotEmpty()) {
                    $status = 'completed';
                } elseif ($matchingAssignments->where('status', 'in_progress')->isNotEmpty()) {
                    $status = 'in_progress';
                } else {
                    $status = 'pending';
                }
            }

            $matchingCompleted = $completedInspectionsAll->where('dynamic_checklist_id', $plan->checklist_id)
                ->where('sede_id', $plan->sede_id);
            $completedTotal = $matchingCompleted->count();
            $completedPassed = $matchingCompleted->where('result', 'Pass')->count();
            $compliancePct = $completedTotal > 0 ? round(($completedPassed / $completedTotal) * 100) : null;

            $key = $plan->checklist_id . '-' . $plan->sede_id;
            $unifiedInspections->put($key, [
                'id' => $plan->id, // ID del plan
                'type' => 'inspection',
                'title' => $plan->checklist?->title ?? $plan->activity_name,
                'sede' => $plan->sede?->nombre_sede,
                'sede_id' => $plan->sede_id,
                'empresa' => $plan->sede?->empresa?->nombre,
                'due_date' => Carbon::now()->endOfMonth(),
                'status' => $status,
                'checklist_id' => $plan->checklist_id,
                'source' => 'contract',
                'registry_code' => $plan->checklist?->registry_code ?? $plan->activity_code ?? ('AN-' . $plan->id),
                'checklist_count' => $matchingAssignments->count(),
                'completed_count' => $completedTotal,
                'passed_count' => $completedPassed,
                'compliance_pct' => $compliancePct,
            ]);
        }

        foreach ($allAssignments as $assignment) {
            $key = $assignment->dynamic_checklist_id . '-' . $assignment->sede_id;
            if ($unifiedInspections->has($key)) {
                continue;
            }

            $matchingManual = $allAssignments->where('dynamic_checklist_id', $assignment->dynamic_checklist_id)
                ->where('sede_id', $assignment->sede_id);

            $matchingCompleted = $completedInspectionsAll->where('dynamic_checklist_id', $assignment->dynamic_checklist_id)
                ->where('sede_id', $assignment->sede_id);
            $completedTotal = $matchingCompleted->count();
            $completedPassed = $matchingCompleted->where('result', 'Pass')->count();
            $compliancePct = $completedTotal > 0 ? round(($completedPassed / $completedTotal) * 100) : null;

            $unifiedInspections->put($key, [
                'id' => $assignment->id,
                'type' => 'inspection',
                'title' => $assignment->checklist?->title,
                'sede' => $assignment->sede?->nombre_sede,
                'sede_id' => $assignment->sede_id,
                'empresa' => $assignment->sede?->empresa?->nombre,
                'due_date' => $assignment->due_date,
                'status' => $assignment->status,
                'checklist_id' => $assignment->dynamic_checklist_id,
                'source' => 'manual',
                'registry_code' => $assignment->checklist?->registry_code ?? ('TX-' . $assignment->id),
                'checklist_count' => $matchingManual->count(),
                'completed_count' => $completedTotal,
                'passed_count' => $completedPassed,
                'compliance_pct' => $compliancePct,
            ]);
        }
        $inspections = $unifiedInspections->values();

        // 5.2️⃣ Capacitaciones
        $unifiedTrainings = collect();
        $contractTrainings = $contractTasks->where('category', 'Capacitación');

        foreach ($contractTrainings as $plan) {
            $matchingTrainings = $allTrainings->where('sede_id', $plan->sede_id)
                ->filter(function($t) use ($plan) {
                    return ($plan->checklist_id && $t->dynamic_checklist_id == $plan->checklist_id) 
                        || (\Illuminate\Support\Str::slug($t->title) === \Illuminate\Support\Str::slug($plan->activity_name));
                });

            $status = 'scheduled';
            if ($matchingTrainings->isNotEmpty()) {
                if ($matchingTrainings->whereIn('status', ['completed', 'finalizado'])->isNotEmpty()) {
                    $status = 'completed';
                } elseif ($matchingTrainings->where('status', 'in_progress')->isNotEmpty()) {
                    $status = 'in_progress';
                } else {
                    $status = 'pending';
                }
            }

            $key = $plan->checklist_id ? ('c_' . $plan->checklist_id . '_' . $plan->sede_id) : ('t_' . \Illuminate\Support\Str::slug($plan->activity_name) . '_' . $plan->sede_id);

            $unifiedTrainings->put($key, [
                'id' => $plan->id,
                'type' => 'training',
                'title' => $plan->checklist?->title ?? $plan->activity_name,
                'sede' => $plan->sede?->nombre_sede,
                'sede_id' => $plan->sede_id,
                'empresa' => $plan->sede?->empresa?->nombre,
                'due_date' => Carbon::now()->endOfMonth(),
                'status' => $status,
                'checklist_id' => $plan->checklist_id,
                'source' => 'contract',
                'registry_code' => $plan->checklist?->registry_code ?? $plan->activity_code ?? ('AN-' . $plan->id),
            ]);
        }

        foreach ($allTrainings as $t) {
            $key = $t->dynamic_checklist_id ? ('c_' . $t->dynamic_checklist_id . '_' . $t->sede_id) : ('t_' . \Illuminate\Support\Str::slug($t->title) . '_' . $t->sede_id);
            if ($unifiedTrainings->has($key)) {
                continue;
            }

            $unifiedTrainings->put($key, [
                'id' => $t->id,
                'type' => 'training',
                'title' => $t->title,
                'sede' => $t->sede?->nombre_sede,
                'sede_id' => $t->sede_id,
                'empresa' => $t->sede?->empresa?->nombre,
                'due_date' => $t->date,
                'status' => $t->status,
                'checklist_id' => $t->dynamic_checklist_id,
                'source' => 'manual',
                'registry_code' => $t->dynamicChecklist?->registry_code ?? ('EDU-' . $t->id),
            ]);
        }
        $trainings = $unifiedTrainings->values();

        // 5.3️⃣ Informes
        $unifiedReports = collect();
        $contractReports = $contractTasks->where('category', 'Informe');

        foreach ($contractReports as $plan) {
            $matchingReports = $allReports->where('sede_id', $plan->sede_id)
                ->filter(function($r) use ($plan) {
                    return \Illuminate\Support\Str::slug($r->titulo) === \Illuminate\Support\Str::slug($plan->activity_name);
                });

            $status = 'scheduled';
            if ($matchingReports->isNotEmpty()) {
                if ($matchingReports->whereIn('estado', ['completed', 'finalizado', 'cerrado', 'enviado'])->isNotEmpty()) {
                    $status = 'completed';
                } elseif ($matchingReports->where('estado', 'borrador')->isNotEmpty()) {
                    $status = 'in_progress';
                } else {
                    $status = 'pending';
                }
            }

            $key = 'r_' . \Illuminate\Support\Str::slug($plan->activity_name) . '_' . $plan->sede_id;

            $unifiedReports->put($key, [
                'id' => $plan->id,
                'type' => 'report',
                'title' => $plan->checklist?->title ?? $plan->activity_name,
                'sede' => $plan->sede?->nombre_sede,
                'sede_id' => $plan->sede_id,
                'empresa' => $plan->sede?->empresa?->nombre,
                'due_date' => Carbon::now()->endOfMonth(),
                'status' => $status,
                'checklist_id' => null,
                'source' => 'contract',
                'registry_code' => $plan->checklist?->registry_code ?? $plan->activity_code ?? ('AN-' . $plan->id),
            ]);
        }

        foreach ($allReports as $r) {
            $key = 'r_' . \Illuminate\Support\Str::slug($r->titulo) . '_' . $r->sede_id;
            if ($unifiedReports->has($key)) {
                continue;
            }

            $unifiedReports->put($key, [
                'id' => $r->id,
                'type' => 'report',
                'title' => $r->titulo,
                'sede' => $r->sede?->nombre_sede,
                'sede_id' => $r->sede_id,
                'empresa' => $r->sede?->empresa?->nombre,
                'due_date' => $r->created_at->addDays(7),
                'status' => $r->estado === 'borrador' ? 'in_progress' : 'completed',
                'source' => 'manual',
                'registry_code' => 'MET-' . $r->id,
            ]);
        }
        $reports = $unifiedReports->values();

        // 5.4️⃣ Aplicar Filtro de Estado si se solicita
        if ($selectedStatus !== 'all') {
            $inspections = $inspections->filter(function($item) use ($selectedStatus) {
                if ($selectedStatus === 'completed') {
                    return $item['status'] === 'completed';
                } elseif ($selectedStatus === 'in_progress') {
                    return $item['status'] === 'in_progress';
                } else {
                    return $item['status'] === 'scheduled' || $item['status'] === 'pending';
                }
            })->values();

            $trainings = $trainings->filter(function($item) use ($selectedStatus) {
                if ($selectedStatus === 'completed') {
                    return $item['status'] === 'completed';
                } elseif ($selectedStatus === 'in_progress') {
                    return $item['status'] === 'in_progress';
                } else {
                    return $item['status'] === 'scheduled' || $item['status'] === 'pending';
                }
            })->values();

            $reports = $reports->filter(function($item) use ($selectedStatus) {
                if ($selectedStatus === 'completed') {
                    return $item['status'] === 'completed';
                } elseif ($selectedStatus === 'in_progress') {
                    return $item['status'] === 'in_progress';
                } else {
                    return $item['status'] === 'scheduled' || $item['status'] === 'pending';
                }
            })->values();
        }

        // 6️⃣ KPIs (Rendimiento Global del Técnico, no atado al filtro mensual)
        $totalTasks = $inspections->count() + $trainings->count() + $reports->count();
        $completedTasks = $inspections->where('status', 'completed')->count() + $trainings->where('status', 'completed')->count();

        $globalManualTasks = ChecklistAssignment::where('user_id', $user->id)->count();
        $globalManualCompleted = ChecklistAssignment::where('user_id', $user->id)->whereIn('status', ['completed', 'finalizado'])->count();

        $globalTrainings = \App\Models\Training::where('user_id', $user->id)->count();
        $globalTrainingsCompleted = \App\Models\Training::where('user_id', $user->id)->whereIn('status', ['completed', 'finalizado'])->count();

        $globalTotal = $globalManualTasks + $globalTrainings;
        $globalCompleted = $globalManualCompleted + $globalTrainingsCompleted;

        $kpis = [
            'total_tasks' => $totalTasks,
            'completed' => $completedTasks,
            'percentage' => $globalTotal > 0 ? round(($globalCompleted / $globalTotal) * 100) : 0,
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
                'tab' => $activeTab,
                'status' => $selectedStatus,
            ],
            'empresas' => Empresa::whereIn('id', $assignedEmpresaIds)->get(),
            'sedes' => Sede::whereIn('id', $assignedSedeIds)->get(),
            'checklistTemplates' => \App\Models\DynamicChecklist::where('is_active', 1)->with(['categoryRelation', 'subcategoryRelation'])->get(),
            'trainingTemplates' => \App\Models\Training::where('is_template', true)->get(),
        ]);
    }

    /**
     * Smart execution of a technical task.
     * Starts a visit if not active and redirects to the appropriate workflow.
     */
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
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $activeVisit = $this->visitService->getActiveVisit($user);

        // Security: Check if user is assigned to this sede
        if (!$user->isAdmin() && !$user->sedes()->where('sedes.id', $request->sede_id)->exists()) {
            abort(403, 'No tienes permiso para operar en esta sede.');
        }

        // Handle Active Visit Conflicts
        if ($activeVisit && $activeVisit->sede_id != $request->sede_id) {
            return back()->with('error', 'Ya tienes una visita activa en ' . $activeVisit->sede->nombre_sede . '. Finalízala antes de iniciar otra en ' . \App\Models\Sede::find($request->sede_id)->nombre_sede . '.');
        }

        // Start Visit if none active
        if (!$activeVisit) {
            $sede = \App\Models\Sede::findOrFail($request->sede_id);
            $visitType = $request->type === 'training' ? 'training' : 'checklist';
            $activeVisit = $this->visitService->startVisit($user, $sede, $visitType);
        }

        if ($request->source === 'adhoc') {
            if ($request->type === 'inspection') {
                $checklistId = $request->id;

                $assignment = \App\Models\ChecklistAssignment::create([
                    'user_id' => $user->id,
                    'sede_id' => $request->sede_id,
                    'empresa_id' => $activeVisit->sede->empresa_id,
                    'dynamic_checklist_id' => $checklistId,
                    'due_date' => now(),
                    'status' => 'in_progress',
                    'notes' => 'Asignación ad-hoc iniciada desde Consola',
                ]);

                return redirect()->route('tecnico.checklists.fill', [
                    'id' => $checklistId,
                    'assignment' => $assignment->id,
                    'visit_id' => $activeVisit->id
                ])->with('message', 'Inspección ad-hoc iniciada.');
            } else {
                // Training ad-hoc
                $checklistId = $request->id;

                $title = $request->title;
                if ($checklistId && !$title) {
                    $checklist = \App\Models\DynamicChecklist::find($checklistId);
                    if ($checklist) {
                        $title = $checklist->title;
                    }
                }
                if (!$title) {
                    $title = 'Capacitación Ad-hoc';
                }

                $modality = $request->input('modality') === 'virtual' ? 'virtual' : 'presencial';

                $training = \App\Models\Training::create([
                    'empresa_id' => $activeVisit->sede->empresa_id,
                    'sede_id' => $request->sede_id,
                    'user_id' => $user->id,
                    'created_by_user_id' => $user->id,
                    'is_template' => false,
                    'title' => $title,
                    'type' => $modality,
                    'date' => now(),
                    'status' => 'pending',
                    'dynamic_checklist_id' => $checklistId ?: null,
                    'notes' => 'Capacitación ad-hoc iniciada desde Consola',
                ]);

                $visitTraining = \App\Models\VisitTraining::create([
                    'visit_certificate_id' => $activeVisit->id,
                    'training_id' => $training->id,
                    'is_adhoc' => true,
                    'title' => $training->title,
                    'description' => $request->description ?: 'Capacitación técnica de ' . $training->title,
                    'duration_minutes' => $request->duration ?: 15,
                    'attendees' => [],
                ]);

                return redirect()->route('tecnico.trainings.session', $visitTraining->id)
                    ->with('message', 'Capacitación ad-hoc iniciada.');
            }
        }

        if ($request->type === 'inspection') {
            $assignmentId = null;
            $checklistId = null;

            if ($request->source === 'contract') {
                $plan = \App\Models\AnnualPlan::findOrFail($request->id);
                $checklistId = $plan->checklist_id;
                
                // Logic to find or create the assignment for this plan in this visit
                $assignment = \App\Models\ChecklistAssignment::where('user_id', $user->id)
                    ->where('sede_id', $request->sede_id)
                    ->where('notes', 'LIKE', '%desde Plan Anual #' . $plan->id . '%')
                    ->first();

                if (!$assignment) {
                    $assignment = \App\Models\ChecklistAssignment::create([
                        'user_id' => $user->id,
                        'sede_id' => $request->sede_id,
                        'empresa_id' => $activeVisit->sede->empresa_id,
                        'dynamic_checklist_id' => $checklistId,
                        'due_date' => now(),
                        'status' => 'pending',
                        'notes' => 'Vínculo automático desde Plan Anual #' . $plan->id,
                    ]);
                }
                $assignmentId = $assignment->id;
            } else {
                // Manual Task
                $assignment = \App\Models\ChecklistAssignment::findOrFail($request->id);
                $assignmentId = $assignment->id;
                $checklistId = $assignment->dynamic_checklist_id;
            }

            return redirect()->route('tecnico.checklists.fill', [
                'id' => $checklistId,
                'assignment' => $assignmentId,
                'visit_id' => $activeVisit->id
            ])->with('message', 'Inspección asignada e iniciada.');

        } else {
            // Training execution
            $visitTraining = null;

            if ($request->source === 'contract') {
                $plan = \App\Models\AnnualPlan::findOrFail($request->id);

                $training = \App\Models\Training::where('user_id', $user->id)
                    ->where('sede_id', $request->sede_id)
                    ->where('notes', 'LIKE', '%desde Plan Anual #' . $plan->id . '%')
                    ->first();

                if (!$training) {
                    $training = \App\Models\Training::create([
                        'empresa_id' => $activeVisit->sede->empresa_id,
                        'sede_id' => $request->sede_id,
                        'user_id' => $user->id,
                        'created_by_user_id' => $user->id,
                        'is_template' => false,
                        'title' => $plan->activity_name,
                        'type' => 'presencial',
                        'date' => now(),
                        'status' => 'pending',
                        'duration' => $plan->duration_minutes ?? 15,
                        'dynamic_checklist_id' => $plan->checklist_id,
                        'notes' => 'Vínculo automático desde Plan Anual #' . $plan->id,
                    ]);
                }

                $visitTraining = \App\Models\VisitTraining::where('visit_certificate_id', $activeVisit->id)
                    ->where('training_id', $training->id)
                    ->first();

                if (!$visitTraining) {
                    $visitTraining = \App\Models\VisitTraining::create([
                        'visit_certificate_id' => $activeVisit->id,
                        'training_id' => $training->id,
                        'is_adhoc' => false,
                        'title' => $training->title,
                        'description' => $training->description ?? 'Capacitación programada de plan anual',
                        'duration_minutes' => $training->duration ?? $plan->duration_minutes ?? 15,
                        'attendees' => [],
                    ]);
                }
            } else {
                // Manual Task
                $training = \App\Models\Training::findOrFail($request->id);

                $visitTraining = \App\Models\VisitTraining::where('visit_certificate_id', $activeVisit->id)
                    ->where('training_id', $training->id)
                    ->first();

                if (!$visitTraining) {
                    $visitTraining = \App\Models\VisitTraining::create([
                        'visit_certificate_id' => $activeVisit->id,
                        'training_id' => $training->id,
                        'is_adhoc' => true,
                        'title' => $training->title,
                        'description' => $training->description ?? 'Capacitación manual',
                        'duration_minutes' => $training->duration ?? 15,
                        'attendees' => [],
                    ]);
                }
            }

            return redirect()->route('tecnico.trainings.session', $visitTraining->id)
                ->with('message', 'Capacitación asignada e iniciada.');
        }
    }

    /**
     * Show modal/page to start a new visit.
     */
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $sedes = $user->isAdmin() 
            ? Sede::with('empresa')->orderBy('nombre_sede')->get()
            : $user->sedes()->with('empresa')->orderBy('nombre_sede')->get();
        
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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Seguridad: Solo el técnico asignado o un admin
        if ($visit->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        // Redirección inteligente si la visita es únicamente para una capacitación
        if ($visit->visit_type === 'training') {
            $visitTraining = \App\Models\VisitTraining::where('visit_certificate_id', $visit->id)->first();
            if ($visitTraining) {
                return redirect()->route('tecnico.trainings.session', $visitTraining->id)
                    ->with('info', 'Sesión de capacitación reanudada automáticamente.');
            }
        }

        if ($visit->status === 'completed' || $visit->status === 'signed') {
            return redirect()->route('gestor.operations.visits.index')
                ->with('info', 'Esta visita ya ha sido finalizada.');
        }

        // Cargar contexto completo
        $visit->load([
            'sede.empresa',
            'checklistCompletions.assignment.checklist',
            'checklistCompletions.assignment.inspection',
            'trainings.training.attendances',
            'hallazgos',
            'incidentes'
        ]);

        // Tareas del Plan Anual para este mes (excluir Informes — son trabajo personal)
        $now = \Carbon\Carbon::now();
        $queryMonth = $now->year . '-' . str_pad($now->month, 2, '0', STR_PAD_LEFT);
        $endOfMonth = $now->copy()->endOfMonth()->toDateString();

        $suggestedPlanTasks = \App\Models\AnnualPlan::with(['checklist'])
            ->where('sede_id', $visit->sede_id)
            ->whereJsonContains('scheduled_months', $queryMonth)
            ->where('category', '!=', 'Informe') // Informes: trabajo personal, no de visita
            ->get()
            ->map(function ($plan) use ($endOfMonth) {
                return [
                    'id'            => $plan->id,
                    'activity_name' => $plan->activity_name,
                    'activity_code' => $plan->activity_code,
                    'category'      => $plan->category,
                    'checklist_id'  => $plan->checklist_id,
                    'due_date'      => $endOfMonth, // vence al final del mes corriente
                ];
            });

        // Misiones Pendientes para esta Sede (inspecciones ya asignadas, no finalizadas)
        $pendingAssignments = \App\Models\ChecklistAssignment::with(['checklist'])
            ->where('sede_id', $visit->sede_id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->get();

        // Capacitaciones Pendientes para esta Sede (del técnico, no completadas)
        $pendingTrainings = \App\Models\Training::with(['dynamicChecklist'])
            ->where('sede_id', $visit->sede_id)
            ->where('user_id', $user->id)
            ->where('status', '!=', 'completed')
            ->get();

        return Inertia::render('Tecnico/Visits/InProgress', [
            'visit'              => $visit,
            'user'               => $user,
            'suggestedPlanTasks' => $suggestedPlanTasks,
            'pendingAssignments' => $pendingAssignments,
            'pendingTrainings'   => $pendingTrainings,
            'templates'          => \App\Models\Training::where('is_template', true)->get(),
            'checklistTemplates' => \App\Models\DynamicChecklist::where('is_active', 1)->with(['categoryRelation', 'subcategoryRelation'])->get(),
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
            ->with(['sede.empresa', 'user'])
            ->where('user_id', $user->id)
            ->latest('visit_date')
            ->latest('id');

        if ($request->search) {
            $query->whereHas('sede', function ($q) use ($request) {
                $q->where('nombre_sede', 'like', "%{$request->search}%");
            });
        }

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        return Inertia::render('Tecnico/Visits/History', [
            'visits' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Finalize the visit.
     */
    public function finalize(Request $request, VisitCertificate $visit)
    {
        \Illuminate\Support\Facades\Log::info('Finalizando visita', [
            'visit_id' => $visit->id,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'has_signature' => !empty($request->signature),
            'signature_unavailable' => $request->signature_unavailable,
            'payload' => $request->except(['signature']),
        ]);

        try {
            $request->validate([
                'notes' => 'nullable|string',
                'responsible_name' => 'required|string',
                'signature' => 'required_without:signature_skip_reason|nullable|string',
                'signature_skip_reason' => 'required_if:signature_unavailable,true|nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::warning('Fallo de validación al finalizar visita', [
                'errors' => $e->errors()
            ]);
            throw $e;
        }

        $this->visitService->finalizeVisit(
            $visit,
            $request->notes,
            $request->signature,
            $request->responsible_name,
            $request->email_responsable,
            $request->signature_skip_reason
        );

        // Redirigir según el contexto (Técnico o Gestor/Admin)
        $redirectRoute = str_contains($request->route()->getName(), 'tecnico') 
            ? 'tecnico.visits.index' 
            : 'gestor.operations.visits.index';

        return redirect()->route($redirectRoute)
            ->with('message', '¡Visita finalizada y certificada exitosamente!');
    }

    /**
     * Assign a suggested task from the Annual Plan to the current visit context.
     */
    public function assignSuggestedTask(Request $request, VisitCertificate $visit)
    {
        $request->validate([
            'plan_id' => 'required|exists:annual_plans,id',
        ]);

        $plan = \App\Models\AnnualPlan::findOrFail($request->plan_id);

        if ($plan->category === 'Capacitación') {
            $training = \App\Models\Training::create([
                'empresa_id' => $visit->sede->empresa_id,
                'sede_id' => $visit->sede_id,
                'user_id' => $visit->user_id,
                'created_by_user_id' => $visit->user_id,
                'is_template' => false,
                'title' => $plan->activity_name,
                'type' => 'presencial',
                'date' => now(),
                'status' => 'pending',
                'duration' => $plan->duration_minutes ?? 15,
                'dynamic_checklist_id' => $plan->checklist_id,
                'notes' => 'Vínculo automático desde Plan Anual #' . $plan->id,
            ]);

            // Crear el registro de sesión de capacitación en la visita de forma automática
            $visitTraining = \App\Models\VisitTraining::create([
                'visit_certificate_id' => $visit->id,
                'training_id' => $training->id,
                'is_adhoc' => false,
                'title' => $training->title,
                'description' => $training->description ?? 'Capacitación programada de plan anual',
                'duration_minutes' => $training->duration ?? $plan->duration_minutes ?? 15,
                'attendees' => [],
            ]);

            return redirect()->route('tecnico.trainings.session', $visitTraining->id)
                ->with('message', 'Capacitación iniciada e incorporada a la visita.');
        } else {
            /** @var \App\Models\ChecklistAssignment $assignment */
            $assignment = \App\Models\ChecklistAssignment::create([
                'user_id' => $visit->user_id,
                'sede_id' => $visit->sede_id,
                'empresa_id' => $visit->sede->empresa_id, // Agregado para cumplir restricción NOT NULL
                'dynamic_checklist_id' => $plan->checklist_id,
                'due_date' => now(),
                'status' => 'pending',
                'notes' => 'Vínculo automático desde Plan Anual #' . $plan->id,
            ]);

            return redirect()->route('tecnico.checklists.fill', [
                'id' => $assignment->dynamic_checklist_id,
                'assignment' => $assignment->id,
                'visit_id' => $visit->id
            ])->with('message', 'Inspección asignada e iniciada.');
        }
    }

    /**
     * Mapea una asignación manual (Inspección).
     */
    private function mapInspection($a)
    {
        return [
            'id' => $a->id,
            'type' => 'inspection',
            'title' => $a->checklist?->title,
            'sede' => $a->sede?->nombre_sede,
            'sede_id' => $a->sede_id,
            'empresa' => $a->sede?->empresa?->nombre,
            'due_date' => $a->due_date,
            'status' => $a->status,
            'checklist_id' => $a->dynamic_checklist_id,
            'source' => 'manual',
            'registry_code' => $a->checklist?->registry_code ?? ('TX-' . $a->id),
        ];
    }

    /**
     * Mapea una capacitación manual.
     */
    private function mapTraining($t)
    {
        return [
            'id' => $t->id,
            'type' => 'training',
            'title' => $t->title,
            'sede' => $t->sede?->nombre_sede,
            'sede_id' => $t->sede_id,
            'empresa' => $t->sede?->empresa?->nombre,
            'due_date' => $t->date,
            'status' => $t->status,
            'source' => 'manual',
            'registry_code' => $t->dynamicChecklist?->registry_code ?? ('EDU-' . $t->id),
        ];
    }

    /**
     * Mapea un reporte manual.
     */
    private function mapReport($r)
    {
        return [
            'id' => $r->id,
            'type' => 'report',
            'title' => $r->titulo,
            'sede' => $r->sede?->nombre_sede,
            'sede_id' => $r->sede_id,
            'empresa' => $r->sede?->empresa?->nombre,
            'due_date' => $r->created_at->addDays(7),
            'status' => $r->estado,
            'source' => 'manual',
            'registry_code' => 'MET-' . $r->id,
        ];
    }

    /**
     * Mapea una tarea del Plan Anual.
     */
    private function mapContractToTask($p, $type)
    {
        return [
            'id' => $p->id,
            'type' => $type,
            'title' => $p->checklist?->title ?? $p->activity_name,
            'sede' => $p->sede?->nombre_sede,
            'sede_id' => $p->sede_id,
            'empresa' => $p->sede?->empresa?->nombre,
            'due_date' => Carbon::now()->endOfMonth(),
            'status' => 'scheduled',
            'checklist_id' => $p->checklist_id ?? null,
            'source' => 'contract',
            'registry_code' => $p->checklist?->registry_code ?? $p->activity_code ?? ('AN-' . $p->id),
        ];
    }
}
