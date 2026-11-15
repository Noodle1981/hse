<?php

namespace App\Services;

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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TechnicianAssignmentService
{
    public function __construct(private readonly VisitService $visitService) {}

    /**
     * Build the unified Technician Assignments Console payload (inspections, trainings, reports, KPIs).
     */
    public function getMyAssignmentsData(User $user, Request $request): array
    {
        $now = Carbon::now();
        $selectedMonth = $request->month ?? $now->month;
        $search = $request->search;
        $selectedEmpresa = $request->empresa_id;
        $selectedSede = $request->sede_id;
        $activeTab = $request->tab ?? 'inspections';
        $selectedStatus = $request->status ?? 'all';

        $assignedSedes = $user->sedes()->select('sedes.id', 'sedes.empresa_id')->get();
        $assignedSedeIds = $assignedSedes->pluck('id')->toArray();
        $assignedEmpresaIds = $assignedSedes->pluck('empresa_id')->unique()->toArray();

        $activeVisit = $this->visitService->getActiveVisit($user);

        $assignmentsQuery = ChecklistAssignment::with(['checklist.categoryRelation', 'checklist.questions', 'sede.empresa'])
            ->whereIn('sede_id', $assignedSedeIds)
            ->where(fn ($q) => $q->where('user_id', $user->id)->orWhereNull('user_id'))
            ->whereMonth('due_date', $selectedMonth)
            ->whereYear('due_date', $now->year);

        $trainingsQuery = Training::with(['sede.empresa', 'dynamicChecklist.categoryRelation'])
            ->where('user_id', $user->id)
            ->whereIn('sede_id', $assignedSedeIds)
            ->whereMonth('date', $selectedMonth)
            ->whereYear('date', $now->year);

        $reportsQuery = MasterReport::with(['sede.empresa'])
            ->where('user_id', $user->id)
            ->whereIn('sede_id', $assignedSedeIds)
            ->whereMonth('created_at', $selectedMonth)
            ->whereYear('created_at', $now->year);

        $queryMonth = $now->year.'-'.str_pad((string) $selectedMonth, 2, '0', STR_PAD_LEFT);
        $contractQuery = AnnualPlan::with(['checklist.categoryRelation', 'sede.empresa'])
            ->whereIn('sede_id', $assignedSedeIds)
            ->whereJsonContains('scheduled_months', $queryMonth);

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

        $completedInspectionsAll = DynamicInspection::whereIn('sede_id', $assignedSedeIds)
            ->whereMonth('completed_at', $selectedMonth)
            ->whereYear('completed_at', $now->year)
            ->where('status', 'completed')
            ->get();

        $inspections = $this->buildUnifiedInspections($contractTasks->where('category', 'Inspección'), $allAssignments, $completedInspectionsAll);
        $trainings = $this->buildUnifiedTrainings($contractTasks->where('category', 'Capacitación'), $allTrainings);
        $reports = $this->buildUnifiedReports($contractTasks->where('category', 'Informe'), $allReports, $queryMonth, $now->year, (int) $selectedMonth);

        if ($selectedStatus !== 'all') {
            $inspections = $this->filterByStatus($inspections, $selectedStatus);
            $trainings = $this->filterByStatus($trainings, $selectedStatus);
            $reports = $this->filterByStatus($reports, $selectedStatus);
        }

        $kpis = $this->calculateTechnicianKpis($user, $inspections, $trainings, $reports);

        return [
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
        ];
    }

    private function buildUnifiedInspections(Collection $contractInspections, Collection $allAssignments, Collection $completedInspectionsAll): Collection
    {
        $unified = collect();

        foreach ($contractInspections as $plan) {
            $matchingAssignments = $allAssignments->where('dynamic_checklist_id', $plan->checklist_id)
                ->where('sede_id', $plan->sede_id);

            $status = $this->resolveGroupStatus($matchingAssignments);
            $matchingCompleted = $completedInspectionsAll->where('dynamic_checklist_id', $plan->checklist_id)
                ->where('sede_id', $plan->sede_id);
            $completedTotal = $matchingCompleted->count();
            $completedPassed = $matchingCompleted->where('result', 'Pass')->count();

            $key = $plan->checklist_id.'-'.$plan->sede_id;
            $unified->put($key, [
                'id' => $plan->id,
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
                'compliance_pct' => $completedTotal > 0 ? round(($completedPassed / $completedTotal) * 100) : null,
            ]);
        }

        foreach ($allAssignments as $assignment) {
            $key = $assignment->dynamic_checklist_id.'-'.$assignment->sede_id;
            if ($unified->has($key)) {
                continue;
            }

            $matchingManual = $allAssignments->where('dynamic_checklist_id', $assignment->dynamic_checklist_id)
                ->where('sede_id', $assignment->sede_id);
            $matchingCompleted = $completedInspectionsAll->where('dynamic_checklist_id', $assignment->dynamic_checklist_id)
                ->where('sede_id', $assignment->sede_id);
            $completedTotal = $matchingCompleted->count();
            $completedPassed = $matchingCompleted->where('result', 'Pass')->count();

            $unified->put($key, [
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
                'compliance_pct' => $completedTotal > 0 ? round(($completedPassed / $completedTotal) * 100) : null,
            ]);
        }

        return $unified->values();
    }

    private function buildUnifiedTrainings(Collection $contractTrainings, Collection $allTrainings): Collection
    {
        $unified = collect();

        foreach ($contractTrainings as $plan) {
            $matchingTrainings = $allTrainings->where('sede_id', $plan->sede_id)
                ->filter(fn ($t) => ($plan->checklist_id && $t->dynamic_checklist_id == $plan->checklist_id)
                    || (Str::slug($t->title) === Str::slug($plan->activity_name)));

            $key = $plan->checklist_id
                ? ('c_'.$plan->checklist_id.'_'.$plan->sede_id)
                : ('t_'.Str::slug($plan->activity_name).'_'.$plan->sede_id);

            $unified->put($key, [
                'id' => $plan->id,
                'type' => 'training',
                'title' => $plan->checklist?->title ?? $plan->activity_name,
                'sede' => $plan->sede?->nombre_sede,
                'sede_id' => $plan->sede_id,
                'empresa' => $plan->sede?->empresa?->nombre,
                'due_date' => Carbon::now()->endOfMonth(),
                'status' => $this->resolveGroupStatus($matchingTrainings),
                'checklist_id' => $plan->checklist_id,
                'source' => 'contract',
                'registry_code' => $plan->checklist?->registry_code ?? $plan->activity_code ?? ('AN-'.$plan->id),
            ]);
        }

        foreach ($allTrainings as $t) {
            $key = $t->dynamic_checklist_id
                ? ('c_'.$t->dynamic_checklist_id.'_'.$t->sede_id)
                : ('t_'.Str::slug($t->title).'_'.$t->sede_id);
            if ($unified->has($key)) {
                continue;
            }

            $unified->put($key, [
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

        return $unified->values();
    }

    private function buildUnifiedReports(Collection $contractReports, Collection $allReports, string $queryMonth, int $year, int $selectedMonth): Collection
    {
        $unified = collect();

        foreach ($contractReports as $plan) {
            $matchingReport = MasterReport::where('sede_id', $plan->sede_id)
                ->where('periodo', $queryMonth)
                ->orderByDesc('created_at')
                ->first();

            if (! $matchingReport) {
                $matchingReport = MasterReport::where('sede_id', $plan->sede_id)
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $selectedMonth)
                    ->orderByDesc('created_at')
                    ->first();
            }

            $accion = $this->resolveReportAction($matchingReport, 'ejecutar');
            $key = 'r_'.Str::slug($plan->activity_name).'_'.$plan->sede_id;

            $unified->put($key, [
                'id' => $plan->id,
                'type' => 'report',
                'title' => $plan->checklist?->title ?? $plan->activity_name,
                'sede' => $plan->sede?->nombre_sede,
                'sede_id' => $plan->sede_id,
                'empresa' => $plan->sede?->empresa?->nombre,
                'due_date' => Carbon::now()->endOfMonth(),
                'status' => $this->mapReportActionToStatus($accion),
                'checklist_id' => null,
                'source' => 'contract',
                'registry_code' => $plan->checklist?->registry_code ?? $plan->activity_code ?? ('AN-'.$plan->id),
                'accion' => $accion,
                'master_report_id' => $matchingReport?->id,
                'observacion_revision' => $matchingReport?->observacion_revision,
            ]);
        }

        $usedMasterReportIds = $unified->pluck('master_report_id')->filter()->values()->toArray();

        foreach ($allReports as $r) {
            if (in_array($r->id, $usedMasterReportIds)) {
                continue;
            }

            $key = 'r_'.Str::slug($r->titulo).'_'.$r->sede_id;
            if ($unified->has($key)) {
                continue;
            }

            $accionAdhoc = $this->resolveReportAction($r, 'reanudar');

            $unified->put($key, [
                'id' => $r->id,
                'type' => 'report',
                'title' => $r->titulo,
                'sede' => $r->sede?->nombre_sede,
                'sede_id' => $r->sede_id,
                'empresa' => $r->sede?->empresa?->nombre,
                'due_date' => $r->created_at->addDays(7),
                'status' => $this->mapReportActionToStatus($accionAdhoc),
                'source' => 'manual',
                'registry_code' => 'MET-'.$r->id,
                'accion' => $accionAdhoc,
                'master_report_id' => $r->id,
                'observacion_revision' => $r->observacion_revision,
            ]);
        }

        return $unified->values();
    }

    private function resolveGroupStatus(Collection $items): string
    {
        if ($items->isEmpty()) {
            return 'scheduled';
        }
        if ($items->whereIn('status', ['completed', 'finalizado'])->isNotEmpty()) {
            return 'completed';
        }
        if ($items->where('status', 'in_progress')->isNotEmpty()) {
            return 'in_progress';
        }

        return 'pending';
    }

    private function resolveReportAction(?MasterReport $report, string $fallback): string
    {
        return match (true) {
            is_null($report) => $fallback,
            $report->estado === 'aprobado' => 'aprobado',
            $report->estado === 'finalizado' => 'pendiente',
            $report->estado === 'en_redaccion' && ! is_null($report->observacion_revision) => 'corregir',
            $report->estado === 'en_redaccion' => 'reanudar',
            default => $fallback,
        };
    }

    private function mapReportActionToStatus(string $accion): string
    {
        return match ($accion) {
            'aprobado' => 'completed',
            'pendiente' => 'pending_review',
            'corregir' => 'correction',
            'reanudar' => 'in_progress',
            default => 'scheduled',
        };
    }

    private function filterByStatus(Collection $items, string $selectedStatus): Collection
    {
        return $items->filter(function ($item) use ($selectedStatus) {
            if ($selectedStatus === 'completed') {
                return $item['status'] === 'completed';
            }
            if ($selectedStatus === 'in_progress') {
                return $item['status'] === 'in_progress';
            }

            return $item['status'] === 'scheduled' || $item['status'] === 'pending';
        })->values();
    }

    private function calculateTechnicianKpis(User $user, Collection $inspections, Collection $trainings, Collection $reports): array
    {
        $totalTasks = $inspections->count() + $trainings->count() + $reports->count();
        $completedTasks = $inspections->where('status', 'completed')->count() + $trainings->where('status', 'completed')->count();

        $globalManualTasks = ChecklistAssignment::where('user_id', $user->id)->count();
        $globalManualCompleted = ChecklistAssignment::where('user_id', $user->id)->whereIn('status', ['completed', 'finalizado'])->count();

        $globalTrainings = Training::where('user_id', $user->id)->count();
        $globalTrainingsCompleted = Training::where('user_id', $user->id)->whereIn('status', ['completed', 'finalizado'])->count();

        $globalTotal = $globalManualTasks + $globalTrainings;
        $globalCompleted = $globalManualCompleted + $globalTrainingsCompleted;

        return [
            'total_tasks' => $totalTasks,
            'completed' => $completedTasks,
            'percentage' => $globalTotal > 0 ? round(($globalCompleted / $globalTotal) * 100) : 0,
            'critical_findings' => Hallazgo::where('inspector_id', $user->id)->where('criticidad', 'alta')->where('estado', '!=', 'resuelto')->count(),
        ];
    }
}
