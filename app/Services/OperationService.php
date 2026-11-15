<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\ChecklistAssignment;
use App\Models\DynamicChecklist;
use App\Models\DynamicInspection;
use App\Models\Sede;
use App\Models\Training;
use App\Models\User;
use App\Models\VisitCertificate;
use App\Models\VisitTraining;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OperationService
{
    public function __construct(
        private readonly VisitService $visitService,
        private readonly QuickTaskExecutionService $quickTaskService,
    ) {}

    public function getVisitsIndexData(User $user, Request $request): array
    {
        $query = VisitCertificate::query()->with(['sede.empresa', 'user'])->latest('visit_date');

        if (! $user->isSuperAdmin()) {
            $query->whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $user->consultora_id));
        }
        if ($request->search) {
            $query->whereHas('sede', fn ($q) => $q->where('nombre_sede', 'like', "%{$request->search}%"));
        }
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        return [
            'visits' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'status']),
            'activeVisit' => $this->visitService->getActiveVisit($user),
        ];
    }

    public function startQuickExecution(User $user, Request $request): array
    {
        if (! $user->isAdmin() && ! $user->sedes()->where('sedes.id', $request->sede_id)->exists()) {
            abort(403, 'No tienes permiso para operar en esta sede.');
        }

        $activeVisit = $this->visitService->getActiveVisit($user);
        if ($activeVisit && $activeVisit->sede_id != $request->sede_id) {
            return [
                'error' => 'Ya tienes una visita activa en '.$activeVisit->sede->nombre_sede.'. Finalízala antes de iniciar otra en '.Sede::find($request->sede_id)->nombre_sede.'.',
            ];
        }

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
            return $this->quickTaskService->startAdhocExecution($user, $activeVisit, $request);
        }

        return $request->type === 'inspection'
            ? $this->quickTaskService->startInspectionExecution($user, $activeVisit, $request)
            : $this->quickTaskService->startTrainingExecution($user, $activeVisit, $request);
    }

    public function getCreateVisitSedes(User $user)
    {
        return $user->isAdmin()
            ? Sede::with('empresa')
                ->when(! $user->isSuperAdmin(), fn ($q) => $q->whereHas('empresa', fn ($eq) => $eq->where('consultora_id', $user->consultora_id)))
                ->orderBy('nombre_sede')
                ->get()
            : $user->sedes()->with('empresa')->orderBy('nombre_sede')->get();
    }

    public function startVisitForSede(User $user, int|string $sedeId, ?float $lat, ?float $lng): VisitCertificate
    {
        return $this->visitService->startVisit($user, Sede::findOrFail($sedeId), null, $lat, $lng);
    }

    public function prepareShowVisit(User $user, VisitCertificate $visit): array
    {
        if (! $user->isSuperAdmin() && $visit->consultora_id !== $user->consultora_id) {
            abort(403);
        }
        if ($visit->user_id !== $user->id && ! $user->isAdmin()) {
            abort(403);
        }

        if ($visit->visit_type === 'training') {
            $visitTraining = VisitTraining::where('visit_certificate_id', $visit->id)->first();
            if ($visitTraining) {
                return [
                    'redirect' => route('tecnico.trainings.session', $visitTraining->id),
                    'info' => 'Sesión de capacitación reanudada automáticamente.',
                ];
            }
        }

        if ($visit->status === 'completed' || $visit->status === 'signed') {
            return [
                'redirect' => route('gestor.operations.visits.index'),
                'info' => 'Esta visita ya ha sido finalizada.',
            ];
        }

        $visit->load([
            'sede.empresa',
            'checklistCompletions.assignment.checklist',
            'checklistCompletions.assignment.inspection.asset',
            'trainings.training.attendances',
            'hallazgos',
            'incidentes',
        ]);

        $now = Carbon::now();
        $queryMonth = $now->year.'-'.str_pad((string) $now->month, 2, '0', STR_PAD_LEFT);
        $endOfMonth = $now->copy()->endOfMonth()->toDateString();

        $suggestedPlanTasks = AnnualPlan::with(['checklist'])
            ->where('sede_id', $visit->sede_id)
            ->whereJsonContains('scheduled_months', $queryMonth)
            ->where('category', '!=', 'Informe')
            ->get()
            ->map(fn ($plan) => [
                'id' => $plan->id,
                'activity_name' => $plan->activity_name,
                'activity_code' => $plan->activity_code,
                'category' => $plan->category,
                'checklist_id' => $plan->checklist_id,
                'due_date' => $endOfMonth,
            ]);

        return [
            'props' => [
                'visit' => $visit,
                'user' => $user,
                'suggestedPlanTasks' => $suggestedPlanTasks,
                'pendingAssignments' => ChecklistAssignment::with(['checklist'])
                    ->where('sede_id', $visit->sede_id)->whereIn('status', ['pending', 'in_progress'])->get(),
                'pendingTrainings' => Training::with(['dynamicChecklist'])
                    ->where('sede_id', $visit->sede_id)->where('user_id', $user->id)->where('status', '!=', 'completed')->get(),
                'templates' => Training::where('is_template', true)->get(),
                'checklistTemplates' => DynamicChecklist::where('is_active', 1)->with(['categoryRelation', 'subcategoryRelation'])->get(),
            ],
        ];
    }

    public function getCompletedInspections(Request $request): array
    {
        $query = DynamicInspection::query()->with(['checklist', 'sede', 'user'])->where('status', 'completed')->latest('completed_at');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('sede', fn ($sq) => $sq->where('nombre_sede', 'like', "%{$request->search}%"))
                    ->orWhereHas('checklist', fn ($cq) => $cq->where('title', 'like', "%{$request->search}%"));
            });
        }

        return [
            'inspections' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search']),
        ];
    }

    public function getMyVisitsData(User $user, Request $request): array
    {
        $query = VisitCertificate::query()->with(['sede.empresa', 'user'])->where('user_id', $user->id)->latest('visit_date')->latest('id');

        if ($request->search) {
            $query->whereHas('sede', fn ($q) => $q->where('nombre_sede', 'like', "%{$request->search}%"));
        }
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        return [
            'visits' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'status']),
        ];
    }

    public function checkoutVisit(VisitCertificate $visit, ?float $lat, ?float $lng): VisitCertificate
    {
        if ($lat !== null && $lng !== null) {
            $visit->checkout_lat = $lat;
            $visit->checkout_lng = $lng;
            $visit->loadMissing('sede');
            $this->visitService->timeTrackingService->verifyGpsPresence($visit);
            $visit->save();
        }

        return $visit;
    }

    public function assignSuggestedTask(VisitCertificate $visit, int|string $planId): array
    {
        return $this->quickTaskService->assignSuggestedTask($visit, $planId);
    }
}
