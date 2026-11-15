<?php

namespace App\Http\Controllers;

use App\Models\AnnualPlan;
use App\Models\ChecklistAssignment;
use App\Models\DynamicChecklist;
use App\Models\DynamicInspection;
use App\Models\Empresa;
use App\Models\Hallazgo;
use App\Models\MasterReport;
use App\Models\Sede;
use App\Models\Training;
use App\Models\User;
use App\Models\VisitCertificate;
use App\Models\VisitTraining;
use App\Services\VisitService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
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
        /** @var User $user */
        $user = Auth::user();

        $query = VisitCertificate::query()
            ->with(['sede.empresa', 'user'])
            ->latest('visit_date');

        if (! $user->isSuperAdmin()) {
            $query->whereHas('sede.empresa', function ($q) use ($user) {
                $q->where('consultora_id', $user->consultora_id);
            });
        }

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
        $selectedStatus = $request->status ?? 'all';

        // IDs de Sedes y Empresas ASIGNADAS al técnico
        $assignedSedes = $user->sedes()->select('sedes.id', 'sedes.empresa_id')->get();
        $assignedSedeIds = $assignedSedes->pluck('id')->toArray();
        $assignedEmpresaIds = $assignedSedes->pluck('empresa_id')->unique()->toArray();

        $activeVisit = $this->visitService->getActiveVisit($user);

        // 1️⃣ Manual / Automatic Assignments (Checklists) - LOAD ALL FOR GROUPING
        $assignmentsQuery = ChecklistAssignment::with(['checklist.categoryRelation', 'checklist.questions', 'sede.empresa'])
            ->whereIn('sede_id', $assignedSedeIds)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereNull('user_id');
            })
            ->whereMonth('due_date', $selectedMonth)
            ->whereYear('due_date', $now->year);

        // 2️⃣ Trainings
        $trainingsQuery = Training::with(['sede.empresa', 'dynamicChecklist.categoryRelation'])
            ->where('user_id', $user->id)
            ->whereIn('sede_id', $assignedSedeIds)
            ->whereMonth('date', $selectedMonth)
            ->whereYear('date', $now->year);

        // 3️⃣ Reports
        $reportsQuery = MasterReport::with(['sede.empresa'])
            ->where('user_id', $user->id)
            ->whereIn('sede_id', $assignedSedeIds)
            ->whereMonth('created_at', $selectedMonth)
            ->whereYear('created_at', $now->year);

        // 4️⃣ Annual Plan Tasks
        $queryMonth = $now->year.'-'.str_pad($selectedMonth, 2, '0', STR_PAD_LEFT);
        $contractQuery = AnnualPlan::with(['checklist.categoryRelation', 'sede.empresa'])
            ->whereIn('sede_id', $assignedSedeIds)
            ->whereJsonContains('scheduled_months', $queryMonth);

        // Apply filters (Empresa/Sede/Search)
        if ($selectedEmpresa) {
            $assignmentsQuery->whereHas('sede', fn ($q) => $q->where('empresa_id', $selectedEmpresa));
            $trainingsQuery->whereHas('sede', fn ($q) => $q->where('empresa_id', $selectedEmpresa));
            $reportsQuery->whereHas('sede', fn ($q) => $q->where('empresa_id', $selectedEmpresa));
            $contractQuery->whereHas('sede', fn ($q) => $q->where('empresa_id', $selectedEmpresa));
        }
        if ($selectedSede) {
            $assignmentsQuery->where('sede_id', $selectedSede);
            $trainingsQuery->where('sede_id', $selectedSede);
            $reportsQuery->where('sede_id', $selectedSede);
            $contractQuery->where('sede_id', $selectedSede);
        }
        if ($search) {
            $assignmentsQuery->whereHas('checklist', fn ($q) => $q->where('title', 'like', "%{$search}%"));
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
        $completedInspectionsAll = DynamicInspection::whereIn('sede_id', $assignedSedeIds)
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

            $key = $plan->checklist_id.'-'.$plan->sede_id;
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
                'registry_code' => $plan->checklist?->registry_code ?? $plan->activity_code ?? ('AN-'.$plan->id),
                'checklist_count' => $matchingAssignments->count(),
                'completed_count' => $completedTotal,
                'passed_count' => $completedPassed,
                'compliance_pct' => $compliancePct,
            ]);
        }

        foreach ($allAssignments as $assignment) {
            $key = $assignment->dynamic_checklist_id.'-'.$assignment->sede_id;
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
                'registry_code' => $assignment->checklist?->registry_code ?? ('TX-'.$assignment->id),
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
                ->filter(function ($t) use ($plan) {
                    return ($plan->checklist_id && $t->dynamic_checklist_id == $plan->checklist_id)
                        || (Str::slug($t->title) === Str::slug($plan->activity_name));
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

            $key = $plan->checklist_id ? ('c_'.$plan->checklist_id.'_'.$plan->sede_id) : ('t_'.Str::slug($plan->activity_name).'_'.$plan->sede_id);

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
                'registry_code' => $plan->checklist?->registry_code ?? $plan->activity_code ?? ('AN-'.$plan->id),
            ]);
        }

        foreach ($allTrainings as $t) {
            $key = $t->dynamic_checklist_id ? ('c_'.$t->dynamic_checklist_id.'_'.$t->sede_id) : ('t_'.Str::slug($t->title).'_'.$t->sede_id);
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
                'registry_code' => $t->dynamicChecklist?->registry_code ?? ('EDU-'.$t->id),
            ]);
        }
        $trainings = $unifiedTrainings->values();

        // 5.3️⃣ Informes
        $unifiedReports = collect();
        $contractReports = $contractTasks->where('category', 'Informe');

        foreach ($contractReports as $plan) {
            // Buscar el informe existente usando el campo `periodo` (YYYY-MM) — dato canónico.
            // El campo `periodo` es lo que el técnico seleccionó al crear el informe,
            // independientemente del mes en que fue creado físicamente (created_at).
            $matchingReport = MasterReport::where('sede_id', $plan->sede_id)
                ->where('periodo', $queryMonth)
                ->orderByDesc('created_at')
                ->first();

            // Fallback: si no hay match por periodo exacto, buscar cualquier informe
            // de esta sede en el año actual (cubre casos de periodo mal formateado)
            if (! $matchingReport) {
                $matchingReport = MasterReport::where('sede_id', $plan->sede_id)
                    ->whereYear('created_at', $now->year)
                    ->whereMonth('created_at', $selectedMonth)
                    ->orderByDesc('created_at')
                    ->first();
            }

            // El backend calcula la acción visual que debe mostrar el frontend
            $accion = match (true) {
                is_null($matchingReport) => 'ejecutar',
                $matchingReport->estado === 'aprobado' => 'aprobado',
                $matchingReport->estado === 'finalizado' => 'pendiente',
                $matchingReport->estado === 'en_redaccion' && ! is_null($matchingReport->observacion_revision) => 'corregir',
                $matchingReport->estado === 'en_redaccion' => 'reanudar',
                default => 'ejecutar',
            };

            // Status genérico para el filtro UI (compatible con el selector de estados)
            $status = match ($accion) {
                'aprobado' => 'completed',
                'pendiente' => 'pending_review',
                'corregir' => 'correction',
                'reanudar' => 'in_progress',
                default => 'scheduled',
            };

            $key = 'r_'.Str::slug($plan->activity_name).'_'.$plan->sede_id;

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
                'registry_code' => $plan->checklist?->registry_code ?? $plan->activity_code ?? ('AN-'.$plan->id),
                // Campos nuevos para la lógica de 5 estados
                'accion' => $accion,
                'master_report_id' => $matchingReport?->id,
                'observacion_revision' => $matchingReport?->observacion_revision,
            ]);
        }

        // Recolectar los IDs de MasterReport ya vinculados a filas del plan anual
        // para que el loop ad-hoc no los duplique con otra fila.
        $usedMasterReportIds = $unifiedReports
            ->pluck('master_report_id')
            ->filter()
            ->values()
            ->toArray();

        foreach ($allReports as $r) {
            // Saltar si este MasterReport ya está representado por una fila del plan anual
            if (in_array($r->id, $usedMasterReportIds)) {
                continue;
            }

            $key = 'r_'.Str::slug($r->titulo).'_'.$r->sede_id;
            if ($unifiedReports->has($key)) {
                continue;
            }

            // Informes ad-hoc (no del plan anual): también calculamos su acción
            $accionAdhoc = match (true) {
                $r->estado === 'aprobado' => 'aprobado',
                $r->estado === 'finalizado' => 'pendiente',
                $r->estado === 'en_redaccion' && ! is_null($r->observacion_revision) => 'corregir',
                $r->estado === 'en_redaccion' => 'reanudar',
                default => 'reanudar',
            };

            $unifiedReports->put($key, [
                'id' => $r->id,
                'type' => 'report',
                'title' => $r->titulo,
                'sede' => $r->sede?->nombre_sede,
                'sede_id' => $r->sede_id,
                'empresa' => $r->sede?->empresa?->nombre,
                'due_date' => $r->created_at->addDays(7),
                'status' => match ($accionAdhoc) {
                    'aprobado' => 'completed', 'pendiente' => 'pending_review',
                    'corregir' => 'correction', default => 'in_progress',
                },
                'source' => 'manual',
                'registry_code' => 'MET-'.$r->id,
                'accion' => $accionAdhoc,
                'master_report_id' => $r->id,
                'observacion_revision' => $r->observacion_revision,
            ]);
        }
        $reports = $unifiedReports->values();

        // 5.4️⃣ Aplicar Filtro de Estado si se solicita
        if ($selectedStatus !== 'all') {
            $inspections = $inspections->filter(function ($item) use ($selectedStatus) {
                if ($selectedStatus === 'completed') {
                    return $item['status'] === 'completed';
                } elseif ($selectedStatus === 'in_progress') {
                    return $item['status'] === 'in_progress';
                } else {
                    return $item['status'] === 'scheduled' || $item['status'] === 'pending';
                }
            })->values();

            $trainings = $trainings->filter(function ($item) use ($selectedStatus) {
                if ($selectedStatus === 'completed') {
                    return $item['status'] === 'completed';
                } elseif ($selectedStatus === 'in_progress') {
                    return $item['status'] === 'in_progress';
                } else {
                    return $item['status'] === 'scheduled' || $item['status'] === 'pending';
                }
            })->values();

            $reports = $reports->filter(function ($item) use ($selectedStatus) {
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

        $globalTrainings = Training::where('user_id', $user->id)->count();
        $globalTrainingsCompleted = Training::where('user_id', $user->id)->whereIn('status', ['completed', 'finalizado'])->count();

        $globalTotal = $globalManualTasks + $globalTrainings;
        $globalCompleted = $globalManualCompleted + $globalTrainingsCompleted;

        $kpis = [
            'total_tasks' => $totalTasks,
            'completed' => $completedTasks,
            'percentage' => $globalTotal > 0 ? round(($globalCompleted / $globalTotal) * 100) : 0,
            'critical_findings' => Hallazgo::where('inspector_id', $user->id)->where('criticidad', 'alta')->where('estado', '!=', 'resuelto')->count(),
        ];

        return Inertia::render('Tecnico/MyAssignments', [
            'inspections' => $inspections,
            'trainings' => $trainings,
            'reports' => $reports,
            'kpis' => $kpis,
            'activeVisit' => $activeVisit,
            'filters' => [
                'month' => (int) $selectedMonth,
                'search' => $search,
                'empresa_id' => $selectedEmpresa,
                'sede_id' => $selectedSede,
                'tab' => $activeTab,
                'status' => $selectedStatus,
            ],
            'empresas' => Empresa::whereIn('id', $assignedEmpresaIds)->get(),
            'sedes' => Sede::whereIn('id', $assignedSedeIds)->get(),
            'checklistTemplates' => DynamicChecklist::where('is_active', 1)->with(['categoryRelation', 'subcategoryRelation'])->get(),
            'trainingTemplates' => Training::where('is_template', true)->get(),
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
            'checkin_lat' => 'nullable|numeric',
            'checkin_lng' => 'nullable|numeric',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $activeVisit = $this->visitService->getActiveVisit($user);

        // Security: Check if user is assigned to this sede
        if (! $user->isAdmin() && ! $user->sedes()->where('sedes.id', $request->sede_id)->exists()) {
            abort(403, 'No tienes permiso para operar en esta sede.');
        }

        // Handle Active Visit Conflicts
        if ($activeVisit && $activeVisit->sede_id != $request->sede_id) {
            return back()->with('error', 'Ya tienes una visita activa en '.$activeVisit->sede->nombre_sede.'. Finalízala antes de iniciar otra en '.Sede::find($request->sede_id)->nombre_sede.'.');
        }

        // Start Visit if none active
        if (! $activeVisit) {
            $sede = Sede::findOrFail($request->sede_id);
            $visitType = $request->type === 'training' ? 'training' : 'checklist';
            $activeVisit = $this->visitService->startVisit(
                $user,
                $sede,
                $visitType,
                $request->filled('checkin_lat') ? (float) $request->checkin_lat : null,
                $request->filled('checkin_lng') ? (float) $request->checkin_lng : null
            );
        }

        if ($request->source === 'adhoc') {
            if ($request->type === 'inspection') {
                $checklistId = $request->id;

                $assignment = ChecklistAssignment::create([
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
                    'visit_id' => $activeVisit->id,
                ])->with('message', 'Inspección ad-hoc iniciada.');
            } else {
                // Training ad-hoc
                $checklistId = $request->id;

                $title = $request->title;
                if ($checklistId && ! $title) {
                    $checklist = DynamicChecklist::find($checklistId);
                    if ($checklist) {
                        $title = $checklist->title;
                    }
                }
                if (! $title) {
                    $title = 'Capacitación Ad-hoc';
                }

                $modality = $request->input('modality') === 'virtual' ? 'virtual' : 'presencial';

                $training = Training::create([
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

                $visitTraining = VisitTraining::create([
                    'visit_certificate_id' => $activeVisit->id,
                    'training_id' => $training->id,
                    'is_adhoc' => true,
                    'title' => $training->title,
                    'description' => $request->description ?: 'Capacitación técnica de '.$training->title,
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
                $plan = AnnualPlan::findOrFail($request->id);
                $checklistId = $plan->checklist_id;

                // Logic to find or create the assignment for this plan in this visit
                $assignment = ChecklistAssignment::where('user_id', $user->id)
                    ->where('sede_id', $request->sede_id)
                    ->where('notes', 'LIKE', '%desde Plan Anual #'.$plan->id.'%')
                    ->first();

                if (! $assignment) {
                    $assignment = ChecklistAssignment::create([
                        'user_id' => $user->id,
                        'sede_id' => $request->sede_id,
                        'empresa_id' => $activeVisit->sede->empresa_id,
                        'dynamic_checklist_id' => $checklistId,
                        'due_date' => now(),
                        'status' => 'pending',
                        'notes' => 'Vínculo automático desde Plan Anual #'.$plan->id,
                    ]);
                }
                $assignmentId = $assignment->id;
            } else {
                // Manual Task
                $assignment = ChecklistAssignment::findOrFail($request->id);
                $assignmentId = $assignment->id;
                $checklistId = $assignment->dynamic_checklist_id;
            }

            return redirect()->route('tecnico.checklists.fill', [
                'id' => $checklistId,
                'assignment' => $assignmentId,
                'visit_id' => $activeVisit->id,
            ])->with('message', 'Inspección asignada e iniciada.');

        } else {
            // Training execution
            $visitTraining = null;

            if ($request->source === 'contract') {
                $plan = AnnualPlan::findOrFail($request->id);

                $training = Training::where('user_id', $user->id)
                    ->where('sede_id', $request->sede_id)
                    ->where('notes', 'LIKE', '%desde Plan Anual #'.$plan->id.'%')
                    ->first();

                if (! $training) {
                    $training = Training::create([
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
                        'notes' => 'Vínculo automático desde Plan Anual #'.$plan->id,
                    ]);
                }

                $visitTraining = VisitTraining::where('visit_certificate_id', $activeVisit->id)
                    ->where('training_id', $training->id)
                    ->first();

                if (! $visitTraining) {
                    $visitTraining = VisitTraining::create([
                        'visit_certificate_id' => $activeVisit->id,
                        'training_id' => $training->id,
                        'is_adhoc' => false,
                        'title' => $training->title,
                        'description' => $training->description ?? null,
                        'duration_minutes' => $training->duration ?? $plan->duration_minutes ?? 15,
                        'attendees' => [],
                    ]);
                }
            } else {
                // Manual Task
                $training = Training::findOrFail($request->id);

                $visitTraining = VisitTraining::where('visit_certificate_id', $activeVisit->id)
                    ->where('training_id', $training->id)
                    ->first();

                if (! $visitTraining) {
                    $visitTraining = VisitTraining::create([
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
        /** @var User $user */
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
            'checkin_lat' => 'nullable|numeric',
            'checkin_lng' => 'nullable|numeric',
        ]);

        $sede = Sede::findOrFail($request->sede_id);

        $visit = $this->visitService->startVisit(
            Auth::user(),
            $sede,
            null,
            $request->filled('checkin_lat') ? (float) $request->checkin_lat : null,
            $request->filled('checkin_lng') ? (float) $request->checkin_lng : null
        );

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
        if ($visit->user_id !== $user->id && ! $user->isAdmin()) {
            abort(403);
        }

        // Redirección inteligente si la visita es únicamente para una capacitación
        if ($visit->visit_type === 'training') {
            $visitTraining = VisitTraining::where('visit_certificate_id', $visit->id)->first();
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
            'checklistCompletions.assignment.inspection.asset',
            'trainings.training.attendances',
            'hallazgos',
            'incidentes',
        ]);

        // Tareas del Plan Anual para este mes (excluir Informes — son trabajo personal)
        $now = Carbon::now();
        $queryMonth = $now->year.'-'.str_pad($now->month, 2, '0', STR_PAD_LEFT);
        $endOfMonth = $now->copy()->endOfMonth()->toDateString();

        $suggestedPlanTasks = AnnualPlan::with(['checklist'])
            ->where('sede_id', $visit->sede_id)
            ->whereJsonContains('scheduled_months', $queryMonth)
            ->where('category', '!=', 'Informe') // Informes: trabajo personal, no de visita
            ->get()
            ->map(function ($plan) use ($endOfMonth) {
                return [
                    'id' => $plan->id,
                    'activity_name' => $plan->activity_name,
                    'activity_code' => $plan->activity_code,
                    'category' => $plan->category,
                    'checklist_id' => $plan->checklist_id,
                    'due_date' => $endOfMonth, // vence al final del mes corriente
                ];
            });

        // Misiones Pendientes para esta Sede (inspecciones ya asignadas, no finalizadas)
        $pendingAssignments = ChecklistAssignment::with(['checklist'])
            ->where('sede_id', $visit->sede_id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->get();

        // Capacitaciones Pendientes para esta Sede (del técnico, no completadas)
        $pendingTrainings = Training::with(['dynamicChecklist'])
            ->where('sede_id', $visit->sede_id)
            ->where('user_id', $user->id)
            ->where('status', '!=', 'completed')
            ->get();

        return Inertia::render('Tecnico/Visits/InProgress', [
            'visit' => $visit,
            'user' => $user,
            'suggestedPlanTasks' => $suggestedPlanTasks,
            'pendingAssignments' => $pendingAssignments,
            'pendingTrainings' => $pendingTrainings,
            'templates' => Training::where('is_template', true)->get(),
            'checklistTemplates' => DynamicChecklist::where('is_active', 1)->with(['categoryRelation', 'subcategoryRelation'])->get(),
        ]);
    }

    /**
     * Display a listing of completed inspections (History).
     */
    public function inspections(Request $request)
    {
        $query = DynamicInspection::query()
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
    public function showInspection(DynamicInspection $inspection)
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
        Log::info('Finalizando visita', [
            'visit_id' => $visit->id,
            'user_id' => Auth::id(),
            'has_signature' => ! empty($request->signature),
            'signature_unavailable' => $request->signature_unavailable,
            'payload' => $request->except(['signature']),
        ]);

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
            Log::warning('Fallo de validación al finalizar visita', [
                'errors' => $e->errors(),
            ]);
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

        // Redirigir según el contexto (Técnico o Gestor/Admin)
        $redirectRoute = str_contains($request->route()->getName(), 'tecnico')
            ? 'tecnico.visits.index'
            : 'gestor.operations.visits.index';

        return redirect()->route($redirectRoute)
            ->with('message', '¡Visita finalizada y certificada exitosamente!');
    }

    /**
     * Checkout explícito / actualización de geolocalización de salida.
     */
    public function checkout(Request $request, VisitCertificate $visit)
    {
        $request->validate([
            'checkout_lat' => 'nullable|numeric',
            'checkout_lng' => 'nullable|numeric',
        ]);

        if ($request->filled('checkout_lat') && $request->filled('checkout_lng')) {
            $visit->checkout_lat = (float) $request->checkout_lat;
            $visit->checkout_lng = (float) $request->checkout_lng;
            $visit->loadMissing('sede');
            $this->visitService->timeTrackingService->verifyGpsPresence($visit);
            $visit->save();
        }

        return response()->json([
            'success' => true,
            'gps_verified' => $visit->gps_verified,
            'message' => 'Geolocalización de salida procesada correctamente.',
        ]);
    }

    /**
     * Assign a suggested task from the Annual Plan to the current visit context.
     */
    public function assignSuggestedTask(Request $request, VisitCertificate $visit)
    {
        $request->validate([
            'plan_id' => 'required|exists:annual_plans,id',
        ]);

        $plan = AnnualPlan::findOrFail($request->plan_id);

        if ($plan->category === 'Capacitación') {
            $training = Training::create([
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
                'notes' => 'Vínculo automático desde Plan Anual #'.$plan->id,
            ]);

            // Crear el registro de sesión de capacitación en la visita de forma automática
            $visitTraining = VisitTraining::create([
                'visit_certificate_id' => $visit->id,
                'training_id' => $training->id,
                'is_adhoc' => false,
                'title' => $training->title,
                'description' => $training->description ?? null,
                'duration_minutes' => $training->duration ?? $plan->duration_minutes ?? 15,
                'attendees' => [],
            ]);

            return redirect()->route('tecnico.trainings.session', $visitTraining->id)
                ->with('message', 'Capacitación iniciada e incorporada a la visita.');
        } else {
            /** @var ChecklistAssignment $assignment */
            $assignment = ChecklistAssignment::create([
                'user_id' => $visit->user_id,
                'sede_id' => $visit->sede_id,
                'empresa_id' => $visit->sede->empresa_id, // Agregado para cumplir restricción NOT NULL
                'dynamic_checklist_id' => $plan->checklist_id,
                'due_date' => now(),
                'status' => 'pending',
                'notes' => 'Vínculo automático desde Plan Anual #'.$plan->id,
            ]);

            return redirect()->route('tecnico.checklists.fill', [
                'id' => $assignment->dynamic_checklist_id,
                'assignment' => $assignment->id,
                'visit_id' => $visit->id,
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
            'registry_code' => $a->checklist?->registry_code ?? ('TX-'.$a->id),
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
            'registry_code' => $t->dynamicChecklist?->registry_code ?? ('EDU-'.$t->id),
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
            'registry_code' => 'MET-'.$r->id,
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
            'registry_code' => $p->checklist?->registry_code ?? $p->activity_code ?? ('AN-'.$p->id),
        ];
    }
}
