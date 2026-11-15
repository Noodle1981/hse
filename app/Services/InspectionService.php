<?php

namespace App\Services;

use App\Events\InspectionCompleted;
use App\Http\Requests\StoreChecklistCompletionRequest;
use App\Models\ChecklistAssignment;
use App\Models\DynamicAnswer;
use App\Models\DynamicChecklist;
use App\Models\DynamicInspection;
use App\Models\User;
use App\Models\VisitCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InspectionService
{
    public function __construct(
        private readonly VisitService $visitService,
        private readonly InspectionHistoryService $historyService,
        private readonly InspectionAssetSyncService $assetSyncService,
    ) {}

    public function prepareWizardData(User $user, Request $request, int|string $checklistId, int|string|null $assignmentId = null): array
    {
        $checklist = DynamicChecklist::with(['sections.questions'])->findOrFail($checklistId);
        $assignmentId = $assignmentId ?: $request->query('assignment');
        $visitId = $request->query('visit_id');

        if (! $assignmentId && $visitId) {
            $visit = VisitCertificate::find($visitId);
            if ($visit) {
                $assignment = ChecklistAssignment::create([
                    'user_id' => $user->id,
                    'sede_id' => $visit->sede_id,
                    'empresa_id' => $visit->empresa_id,
                    'dynamic_checklist_id' => $checklist->id,
                    'due_date' => now(),
                    'status' => 'in_progress',
                    'notes' => 'Vínculo automático al vuelo para visita #'.$visit->id,
                ]);
                $assignmentId = $assignment->id;
            }
        }

        $assignment = null;
        if ($assignmentId) {
            $assignment = ChecklistAssignment::where('id', $assignmentId)
                ->where(fn ($q) => $q->where('user_id', $user->id)->orWhereNull('user_id'))
                ->firstOrFail();

            if (! $user->isSuperAdmin() && ! $user->sedes()->where('sedes.id', $assignment->sede_id)->exists()) {
                abort(403, 'No tienes permiso para operar en esta sede.');
            }

            if ($assignment->status === 'completed') {
                return [
                    'already_completed' => true,
                    'redirect' => $visitId ? route('tecnico.visits.in-progress', $visitId) : route('tecnico.checklists.tasks.index'),
                ];
            }

            $assignment->update(['status' => 'in_progress', 'user_id' => $user->id]);
        }

        $inspection = $this->findOrCreateDraftInspection($user, $checklist->id, $assignmentId, $assignment?->sede_id);
        $answers = $inspection->answers()->pluck('value', 'dynamic_question_id');

        $sedeId = $inspection->sede_id ?? $assignment?->sede_id ?: ($visitId ? VisitCertificate::find($visitId)?->sede_id : null);
        $assets = $sedeId ? $this->assetSyncService->getLinkedAssetsForChecklist((int) $sedeId, (int) $checklist->id) : [];
        $inspectedAssetIds = $visitId ? $this->assetSyncService->getInspectedAssetIdsInVisit((int) $visitId) : [];

        return [
            'already_completed' => false,
            'props' => [
                'checklist' => $checklist,
                'assignment' => $assignment,
                'inspection' => $inspection,
                'visitId' => $visitId,
                'initialAnswers' => $answers,
                'assets' => $assets,
                'inspectedAssetIds' => $inspectedAssetIds,
            ],
        ];
    }

    public function saveProgressById(Request $request, int|string $inspectionId): void
    {
        $this->saveProgress($request, DynamicInspection::findOrFail($inspectionId));
    }

    public function saveProgress(Request $request, DynamicInspection $inspection): void
    {
        DB::transaction(function () use ($request, $inspection) {
            $answers = $request->input('answers', []);
            $inspection->answers()->delete();

            if (! empty($answers)) {
                $batchData = [];
                foreach ($answers as $questionId => $value) {
                    $batchData[] = [
                        'dynamic_inspection_id' => $inspection->id,
                        'dynamic_question_id' => $questionId,
                        'value' => is_array($value) ? json_encode($value) : $value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                DynamicAnswer::insert($batchData);
            }

            $updateData = ['notes' => $request->input('notes')];
            if ($request->filled('asset_id')) {
                $assetId = $request->input('asset_id');
                $this->assetSyncService->assertAssetNotBlocked((string) $assetId, (int) $inspection->dynamic_checklist_id);
                $updateData['asset_id'] = $assetId;
            }

            $inspection->update($updateData);
        });
    }

    public function completeInspection(StoreChecklistCompletionRequest $request, User $user, int|string $inspectionId): string
    {
        $inspection = DynamicInspection::findOrFail($inspectionId);

        $timeFields = ['completed_at', 'date', 'time', 'timestamp'];
        if ($request->hasAny($timeFields)) {
            Log::warning('[SRE SECURITY AUDIT] Posible intento de manipulación/fraude de horarios en checklist.', [
                'user_id' => $user->id,
                'user_name' => $user->name ?? $user->nombres,
                'visit_id' => $request->input('visit_id'),
                'ip' => $request->ip(),
                'time_payload' => $request->only($timeFields),
            ]);
        }

        $safeData = $request->validatedWithSafeTime();

        DB::transaction(function () use ($request, $user, $inspection, $safeData) {
            $this->saveProgress($request, $inspection);

            $inspection->update([
                'status' => 'completed',
                'completed_at' => now(),
                'notes' => $safeData['notes'] ?? null,
                'supervisor_name' => $safeData['supervisor_name'],
                'supervisor_signature' => $this->resolveSupervisorSignature($request, $inspection->supervisor_signature),
                'technician_signature' => $user->firma,
                'result' => $inspection->evaluateResult(),
            ]);

            InspectionCompleted::dispatch($inspection);

            if ($inspection->checklist_assignment_id) {
                $assignment = ChecklistAssignment::find($inspection->checklist_assignment_id);
                if ($assignment) {
                    $visit = $request->visit_id ? VisitCertificate::find($request->visit_id) : null;
                    $visit ? $this->visitService->completeChecklist($visit, $assignment, []) : $assignment->update(['status' => 'completed']);
                }
            }
        });

        return $request->visit_id
            ? route('tecnico.visits.in-progress', $request->visit_id)
            : ($user->isTechnician() ? route('tecnico.checklists.tasks.index') : route('gestor.inspections.index'));
    }

    public function getGestorHistory(User $user, Request $request): array
    {
        return $this->historyService->getGestorHistory($user, $request);
    }

    public function getTecnicoHistory(User $user, Request $request): array
    {
        return $this->historyService->getTecnicoHistory($user, $request);
    }

    public function relinkAsset(User $user, DynamicInspection $inspection, ?int $newAssetId): void
    {
        $this->assetSyncService->relinkAsset($user, $inspection, $newAssetId);
    }

    private function findOrCreateDraftInspection(User $user, int $checklistId, int|string|null $assignmentId, ?int $sedeId): DynamicInspection
    {
        $query = DynamicInspection::where('dynamic_checklist_id', $checklistId)->where('user_id', $user->id)->where('status', 'draft');
        $assignmentId ? $query->where('checklist_assignment_id', $assignmentId) : $query->whereNull('checklist_assignment_id');

        return $query->latest()->first() ?? DynamicInspection::create([
            'dynamic_checklist_id' => $checklistId, 'user_id' => $user->id,
            'checklist_assignment_id' => $assignmentId, 'sede_id' => $sedeId, 'status' => 'draft',
        ]);
    }

    private function resolveSupervisorSignature(Request $request, ?string $currentPath): ?string
    {
        if ($request->hasFile('supervisor_signature')) {
            return $request->file('supervisor_signature')->store('signatures', 'public');
        }
        if ($request->filled('supervisor_signature')) {
            $data = $request->input('supervisor_signature');
            if (Str::startsWith($data, 'data:image')) {
                $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $data);
                $fileName = 'signatures/'.Str::random(40).'.png';
                Storage::disk('public')->put($fileName, base64_decode($base64));

                return $fileName;
            }

            return $data;
        }

        return $currentPath;
    }
}
