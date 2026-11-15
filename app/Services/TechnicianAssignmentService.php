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

class TechnicianAssignmentService
{
    public function __construct(
        private readonly VisitService $visitService,
        private readonly TechnicianTaskUnifierService $unifierService,
    ) {}

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

        $inspections = $this->unifierService->buildUnifiedInspections($contractTasks->where('category', 'Inspección'), $allAssignments, $completedInspectionsAll);
        $trainings = $this->unifierService->buildUnifiedTrainings($contractTasks->where('category', 'Capacitación'), $allTrainings);
        $reports = $this->unifierService->buildUnifiedReports($contractTasks->where('category', 'Informe'), $allReports, $queryMonth, $now->year, (int) $selectedMonth);

        if ($selectedStatus !== 'all') {
            $inspections = $this->filterByStatus($inspections, $selectedStatus);
            $trainings = $this->filterByStatus($trainings, $selectedStatus);
            $reports = $this->filterByStatus($reports, $selectedStatus);
        }

        return [
            'inspections' => $inspections,
            'trainings' => $trainings,
            'reports' => $reports,
            'kpis' => $this->calculateTechnicianKpis($user, $inspections, $trainings, $reports),
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

        $globalTotal = ChecklistAssignment::where('user_id', $user->id)->count() + Training::where('user_id', $user->id)->count();
        $globalCompleted = ChecklistAssignment::where('user_id', $user->id)->whereIn('status', ['completed', 'finalizado'])->count()
            + Training::where('user_id', $user->id)->whereIn('status', ['completed', 'finalizado'])->count();

        return [
            'total_tasks' => $totalTasks,
            'completed' => $completedTasks,
            'percentage' => $globalTotal > 0 ? round(($globalCompleted / $globalTotal) * 100) : 0,
            'critical_findings' => Hallazgo::where('inspector_id', $user->id)->where('criticidad', 'alta')->where('estado', '!=', 'resuelto')->count(),
        ];
    }
}
