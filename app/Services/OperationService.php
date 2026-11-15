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
    public function __construct(private readonly VisitService $visitService) {}

    /**
     * Build paginated visits index data scoped by consultora_id.
     */
    public function getVisitsIndexData(User $user, Request $request): array
    {
        $query = VisitCertificate::query()
            ->with(['sede.empresa', 'user'])
            ->latest('visit_date');

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

    /**
     * Start or resume a quick execution task (adhoc, contract, or manual) inside an active visit.
     *
     * @return array{error?: string, redirect: string, message?: string}
     */
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
            return $this->startAdhocExecution($user, $activeVisit, $request);
        }

        if ($request->type === 'inspection') {
            return $this->startInspectionExecution($user, $activeVisit, $request);
        }

        return $this->startTrainingExecution($user, $activeVisit, $request);
    }

    /**
     * Get sedes available to the user for starting a visit.
     */
    public function getCreateVisitSedes(User $user)
    {
        return $user->isAdmin()
            ? Sede::with('empresa')
                ->when(! $user->isSuperAdmin(), fn ($q) => $q->whereHas('empresa', fn ($eq) => $eq->where('consultora_id', $user->consultora_id)))
                ->orderBy('nombre_sede')
                ->get()
            : $user->sedes()->with('empresa')->orderBy('nombre_sede')->get();
    }

    /**
     * Start a new visit for a sede.
     */
    public function startVisitForSede(User $user, int|string $sedeId, ?float $lat, ?float $lng): VisitCertificate
    {
        $sede = Sede::findOrFail($sedeId);

        return $this->visitService->startVisit($user, $sede, null, $lat, $lng);
    }

    /**
     * Resolve visit console context or redirect target if visit is training-only or already closed.
     */
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
                    ->where('sede_id', $visit->sede_id)
                    ->whereIn('status', ['pending', 'in_progress'])
                    ->get(),
                'pendingTrainings' => Training::with(['dynamicChecklist'])
                    ->where('sede_id', $visit->sede_id)
                    ->where('user_id', $user->id)
                    ->where('status', '!=', 'completed')
                    ->get(),
                'templates' => Training::where('is_template', true)->get(),
                'checklistTemplates' => DynamicChecklist::where('is_active', 1)->with(['categoryRelation', 'subcategoryRelation'])->get(),
            ],
        ];
    }

    /**
     * Build paginated completed inspections listing.
     */
    public function getCompletedInspections(Request $request): array
    {
        $query = DynamicInspection::query()
            ->with(['checklist', 'sede', 'user'])
            ->where('status', 'completed')
            ->latest('completed_at');

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

    /**
     * Build technician's own visits history.
     */
    public function getMyVisitsData(User $user, Request $request): array
    {
        $query = VisitCertificate::query()
            ->with(['sede.empresa', 'user'])
            ->where('user_id', $user->id)
            ->latest('visit_date')
            ->latest('id');

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

    /**
     * Update checkout geolocation and verify GPS presence.
     */
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

    /**
     * Assign a suggested annual plan task to an active visit and return redirect route + message.
     */
    public function assignSuggestedTask(VisitCertificate $visit, int|string $planId): array
    {
        $plan = AnnualPlan::findOrFail($planId);

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

            $visitTraining = VisitTraining::create([
                'visit_certificate_id' => $visit->id,
                'training_id' => $training->id,
                'is_adhoc' => false,
                'title' => $training->title,
                'description' => $training->description ?? null,
                'duration_minutes' => $training->duration ?? $plan->duration_minutes ?? 15,
                'attendees' => [],
            ]);

            return [
                'redirect' => route('tecnico.trainings.session', $visitTraining->id),
                'message' => 'Capacitación iniciada e incorporada a la visita.',
            ];
        }

        $assignment = ChecklistAssignment::create([
            'user_id' => $visit->user_id,
            'sede_id' => $visit->sede_id,
            'empresa_id' => $visit->sede->empresa_id,
            'dynamic_checklist_id' => $plan->checklist_id,
            'due_date' => now(),
            'status' => 'pending',
            'notes' => 'Vínculo automático desde Plan Anual #'.$plan->id,
        ]);

        return [
            'redirect' => route('tecnico.checklists.fill', [
                'id' => $assignment->dynamic_checklist_id,
                'assignment' => $assignment->id,
                'visit_id' => $visit->id,
            ]),
            'message' => 'Inspección asignada e iniciada.',
        ];
    }

    private function startAdhocExecution(User $user, VisitCertificate $activeVisit, Request $request): array
    {
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

            return [
                'redirect' => route('tecnico.checklists.fill', [
                    'id' => $checklistId,
                    'assignment' => $assignment->id,
                    'visit_id' => $activeVisit->id,
                ]),
                'message' => 'Inspección ad-hoc iniciada.',
            ];
        }

        $checklistId = $request->id;
        $title = $request->title;
        if ($checklistId && ! $title) {
            $title = DynamicChecklist::find($checklistId)?->title;
        }
        $title = $title ?: 'Capacitación Ad-hoc';

        $training = Training::create([
            'empresa_id' => $activeVisit->sede->empresa_id,
            'sede_id' => $request->sede_id,
            'user_id' => $user->id,
            'created_by_user_id' => $user->id,
            'is_template' => false,
            'title' => $title,
            'type' => $request->input('modality') === 'virtual' ? 'virtual' : 'presencial',
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

        return [
            'redirect' => route('tecnico.trainings.session', $visitTraining->id),
            'message' => 'Capacitación ad-hoc iniciada.',
        ];
    }

    private function startInspectionExecution(User $user, VisitCertificate $activeVisit, Request $request): array
    {
        if ($request->source === 'contract') {
            $plan = AnnualPlan::findOrFail($request->id);
            $checklistId = $plan->checklist_id;

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
            $assignment = ChecklistAssignment::findOrFail($request->id);
            $assignmentId = $assignment->id;
            $checklistId = $assignment->dynamic_checklist_id;
        }

        return [
            'redirect' => route('tecnico.checklists.fill', [
                'id' => $checklistId,
                'assignment' => $assignmentId,
                'visit_id' => $activeVisit->id,
            ]),
            'message' => 'Inspección asignada e iniciada.',
        ];
    }

    private function startTrainingExecution(User $user, VisitCertificate $activeVisit, Request $request): array
    {
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

            $visitTraining = VisitTraining::firstOrCreate(
                ['visit_certificate_id' => $activeVisit->id, 'training_id' => $training->id],
                [
                    'is_adhoc' => false,
                    'title' => $training->title,
                    'description' => $training->description ?? null,
                    'duration_minutes' => $training->duration ?? $plan->duration_minutes ?? 15,
                    'attendees' => [],
                ]
            );
        } else {
            $training = Training::findOrFail($request->id);

            $visitTraining = VisitTraining::firstOrCreate(
                ['visit_certificate_id' => $activeVisit->id, 'training_id' => $training->id],
                [
                    'is_adhoc' => true,
                    'title' => $training->title,
                    'description' => $training->description ?? 'Capacitación manual',
                    'duration_minutes' => $training->duration ?? 15,
                    'attendees' => [],
                ]
            );
        }

        return [
            'redirect' => route('tecnico.trainings.session', $visitTraining->id),
            'message' => 'Capacitación asignada e iniciada.',
        ];
    }
}
