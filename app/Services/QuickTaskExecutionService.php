<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\ChecklistAssignment;
use App\Models\DynamicChecklist;
use App\Models\Training;
use App\Models\User;
use App\Models\VisitCertificate;
use App\Models\VisitTraining;
use Illuminate\Http\Request;

class QuickTaskExecutionService
{
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

    public function startAdhocExecution(User $user, VisitCertificate $activeVisit, Request $request): array
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
        $title = $request->title ?: ($checklistId ? DynamicChecklist::find($checklistId)?->title : null) ?: 'Capacitación Ad-hoc';

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

    public function startInspectionExecution(User $user, VisitCertificate $activeVisit, Request $request): array
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

    public function startTrainingExecution(User $user, VisitCertificate $activeVisit, Request $request): array
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
