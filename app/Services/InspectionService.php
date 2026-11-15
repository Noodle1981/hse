<?php

namespace App\Services;

use App\Events\InspectionCompleted;
use App\Http\Requests\StoreChecklistCompletionRequest;
use App\Models\Asset;
use App\Models\AssetMetadataLog;
use App\Models\AssetType;
use App\Models\ChecklistAssignment;
use App\Models\DynamicAnswer;
use App\Models\DynamicChecklist;
use App\Models\DynamicInspection;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\User;
use App\Models\VisitCertificate;
use App\Models\VisitChecklistCompletion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InspectionService
{
    public function __construct(private readonly VisitService $visitService) {}

    /**
     * Prepare wizard data or return a redirect URL if the assignment is already completed.
     */
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
                    'redirect' => $visitId
                        ? route('tecnico.visits.in-progress', $visitId)
                        : route('tecnico.checklists.tasks.index'),
                ];
            }

            $assignment->update([
                'status' => 'in_progress',
                'user_id' => $user->id,
            ]);
        }

        $inspection = $this->findOrCreateDraftInspection($user, $checklist->id, $assignmentId, $assignment?->sede_id);
        $answers = $inspection->answers()->pluck('value', 'dynamic_question_id');

        $sedeId = $inspection->sede_id ?? $assignment?->sede_id;
        if (! $sedeId && $visitId) {
            $sedeId = VisitCertificate::find($visitId)?->sede_id;
        }

        $assets = $sedeId ? $this->getLinkedAssetsForChecklist((int) $sedeId, (int) $checklist->id) : [];
        $inspectedAssetIds = $visitId ? $this->getInspectedAssetIdsInVisit((int) $visitId) : [];

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

    /**
     * Save partial progress on an inspection by ID.
     */
    public function saveProgressById(Request $request, int|string $inspectionId): void
    {
        $inspection = DynamicInspection::findOrFail($inspectionId);
        $this->saveProgress($request, $inspection);
    }

    /**
     * Save answers, notes, and SRE asset interlock validation inside a transaction.
     */
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
                $this->assertAssetNotBlocked((int) $assetId, (int) $inspection->dynamic_checklist_id);
                $updateData['asset_id'] = $assetId;
            }

            $inspection->update($updateData);
        });
    }

    /**
     * Complete an inspection, enforce server-side timestamp & technician signature, and sync assignment.
     */
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

            $supervisorSignaturePath = $this->resolveSupervisorSignature($request, $inspection->supervisor_signature);

            $inspection->update([
                'status' => 'completed',
                'completed_at' => now(),
                'notes' => $safeData['notes'] ?? null,
                'supervisor_name' => $safeData['supervisor_name'],
                'supervisor_signature' => $supervisorSignaturePath,
                'technician_signature' => $user->firma,
                'result' => $inspection->evaluateResult(),
            ]);

            InspectionCompleted::dispatch($inspection);

            if ($inspection->checklist_assignment_id) {
                $assignment = ChecklistAssignment::find($inspection->checklist_assignment_id);
                if ($assignment) {
                    $visit = $request->visit_id ? VisitCertificate::find($request->visit_id) : null;
                    if ($visit) {
                        $this->visitService->completeChecklist($visit, $assignment, []);
                    } else {
                        $assignment->update(['status' => 'completed']);
                    }
                }
            }
        });

        return $request->visit_id
            ? route('tecnico.visits.in-progress', $request->visit_id)
            : ($user->isTechnician() ? route('tecnico.checklists.tasks.index') : route('gestor.inspections.index'));
    }

    /**
     * Build grouped inspection history for Gestor.
     */
    public function getGestorHistory(User $user, Request $request): array
    {
        $query = DynamicInspection::completed();
        if (! $user->isSuperAdmin() && $user->consultora_id) {
            $query->whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $user->consultora_id));
        }

        $this->applyCommonHistoryFilters($query, $request);
        if ($request->tecnico_id) {
            $query->where('user_id', $request->tecnico_id);
        }

        $allGroups = $query->select(DB::raw('DATE(completed_at) as completed_date'), 'sede_id', 'user_id')
            ->groupBy(DB::raw('DATE(completed_at)'), 'sede_id', 'user_id')
            ->orderBy(DB::raw('DATE(completed_at)'), 'desc')
            ->orderBy('sede_id', 'desc')
            ->orderBy('user_id', 'desc')
            ->get();

        $paginatedGroups = $this->paginateGroups($allGroups, $request, function ($group) {
            $sede = Sede::with('empresa')->find($group->sede_id);
            $techUser = User::find($group->user_id);

            $visitCertificate = VisitCertificate::where('sede_id', $group->sede_id)
                ->where('user_id', $group->user_id)
                ->whereDate('visit_date', $group->completed_date)
                ->first();

            $inspections = DynamicInspection::with('checklist')
                ->where('user_id', $group->user_id)
                ->where('sede_id', $group->sede_id)
                ->whereDate('completed_at', $group->completed_date)
                ->completed()
                ->latest('completed_at')
                ->get();

            return $this->formatHistoryGroup($group, $sede, $visitCertificate, $inspections, [
                'tecnico_id' => $group->user_id,
                'tecnico_nombre' => $techUser ? $techUser->name : 'Técnico Desconocido',
            ]);
        });

        $empresas = $this->getScopedEmpresas($user);
        $tecnicos = User::where(fn ($q) => $q->where('rol', 'tecnico')->orWhere('rol', 'Técnico'))
            ->when($user->consultora_id, fn ($q) => $q->where('consultora_id', $user->consultora_id))
            ->orderBy('name')
            ->get();

        return [
            'inspections' => $paginatedGroups,
            'empresas' => $empresas,
            'tecnicos' => $tecnicos,
            'filters' => $request->only(['empresa', 'sede', 'fecha_inicio', 'fecha_fin', 'tecnico_id']),
        ];
    }

    /**
     * Build grouped inspection history for Técnico.
     */
    public function getTecnicoHistory(User $user, Request $request): array
    {
        $query = DynamicInspection::where('user_id', $user->id)->completed();
        $this->applyCommonHistoryFilters($query, $request);
        if ($request->checklist_id) {
            $query->where('dynamic_checklist_id', $request->checklist_id);
        }

        $allGroups = $query->select(DB::raw('DATE(completed_at) as completed_date'), 'sede_id')
            ->groupBy(DB::raw('DATE(completed_at)'), 'sede_id')
            ->orderBy(DB::raw('DATE(completed_at)'), 'desc')
            ->orderBy('sede_id', 'desc')
            ->get();

        $paginatedGroups = $this->paginateGroups($allGroups, $request, function ($group) use ($user, $request) {
            $sede = Sede::with('empresa')->find($group->sede_id);

            $visitCertificate = VisitCertificate::where('sede_id', $group->sede_id)
                ->whereDate('visit_date', $group->completed_date)
                ->first();

            $inspections = DynamicInspection::with(['checklist', 'asset'])
                ->where('user_id', $user->id)
                ->where('sede_id', $group->sede_id)
                ->whereDate('completed_at', $group->completed_date)
                ->completed()
                ->when($request->checklist_id, fn ($q) => $q->where('dynamic_checklist_id', $request->checklist_id))
                ->latest('completed_at')
                ->get();

            return $this->formatHistoryGroup($group, $sede, $visitCertificate, $inspections, [], includeAsset: true);
        });

        return [
            'inspections' => $paginatedGroups,
            'empresas' => $this->getScopedEmpresas($user),
            'filters' => $request->only(['empresa', 'sede', 'fecha_inicio', 'fecha_fin', 'checklist_id']),
        ];
    }

    /**
     * Re-link an inspection to another asset, restoring previous metadata and syncing new metadata.
     */
    public function relinkAsset(User $user, DynamicInspection $inspection, ?int $newAssetId): void
    {
        $oldAsset = $inspection->asset;

        DB::transaction(function () use ($user, $inspection, $oldAsset, $newAssetId) {
            $inspection->update(['asset_id' => $newAssetId]);

            if ($oldAsset) {
                $logs = AssetMetadataLog::where('asset_id', $oldAsset->id)
                    ->where('dynamic_inspection_id', $inspection->id)
                    ->get();

                $oldMetadata = $oldAsset->metadata ?? [];
                foreach ($logs as $log) {
                    if ($log->old_value === null) {
                        unset($oldMetadata[$log->field_key]);
                    } else {
                        $decoded = json_decode($log->old_value, true);
                        $oldMetadata[$log->field_key] = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $log->old_value;
                    }
                    $log->delete();
                }
                $oldAsset->metadata = $oldMetadata;
                $oldAsset->save();
            }

            if ($newAssetId) {
                $newAsset = Asset::findOrFail($newAssetId);
                $newMetadata = $newAsset->metadata ?? [];
                $inspection->load('answers.question');

                foreach ($inspection->answers as $answer) {
                    $question = $answer->question;
                    if (! $question || empty($question->vincular_a_metadata)) {
                        continue;
                    }

                    $metricKey = $question->vincular_a_metadata;
                    $val = $answer->value;
                    $oldVal = $newMetadata[$metricKey] ?? null;
                    $oldValStr = $oldVal !== null ? (is_array($oldVal) ? json_encode($oldVal) : (string) $oldVal) : null;
                    $newValStr = $val !== null ? (is_array($val) ? json_encode($val) : (string) $val) : null;

                    if ($oldValStr !== $newValStr) {
                        AssetMetadataLog::create([
                            'asset_id' => $newAsset->id,
                            'dynamic_inspection_id' => $inspection->id,
                            'field_key' => $metricKey,
                            'old_value' => $oldValStr,
                            'new_value' => $newValStr,
                            'changed_by' => $user->id,
                        ]);
                    }

                    $newMetadata[$metricKey] = $val;
                }

                $newAsset->metadata = $newMetadata;
                $newAsset->save();
            }

            Artisan::call('assets:check-alerts');
        });
    }

    private function findOrCreateDraftInspection(User $user, int $checklistId, int|string|null $assignmentId, ?int $sedeId): DynamicInspection
    {
        $query = DynamicInspection::where('dynamic_checklist_id', $checklistId)
            ->where('user_id', $user->id)
            ->where('status', 'draft');

        if ($assignmentId) {
            $query->where('checklist_assignment_id', $assignmentId);
        } else {
            $query->whereNull('checklist_assignment_id');
        }

        return $query->latest()->first() ?? DynamicInspection::create([
            'dynamic_checklist_id' => $checklistId,
            'user_id' => $user->id,
            'checklist_assignment_id' => $assignmentId,
            'sede_id' => $sedeId,
            'status' => 'draft',
        ]);
    }

    private function getLinkedAssetsForChecklist(int $sedeId, int $checklistId)
    {
        $linkedAssetTypeIds = [];
        foreach (AssetType::all() as $assetType) {
            foreach ($assetType->metadata_schema ?? [] as $field) {
                foreach ($field['maintenance_rules'] ?? [] as $rule) {
                    if ((int) ($rule['checklist_resolucion_id'] ?? 0) === $checklistId) {
                        $linkedAssetTypeIds[] = $assetType->id;
                        break 2;
                    }
                }
            }
        }

        $assetQuery = Asset::where('sede_id', $sedeId)->with('assetType');
        if (! empty($linkedAssetTypeIds)) {
            $assetQuery->whereIn('asset_type_id', $linkedAssetTypeIds);
        }

        return $assetQuery->get();
    }

    private function getInspectedAssetIdsInVisit(int $visitId): array
    {
        $completedAssignmentIds = VisitChecklistCompletion::where('visit_certificate_id', $visitId)
            ->pluck('checklist_assignment_id')
            ->toArray();

        return DynamicInspection::whereIn('checklist_assignment_id', $completedAssignmentIds)
            ->where('status', 'completed')
            ->whereNotNull('asset_id')
            ->pluck('asset_id')
            ->toArray();
    }

    private function assertAssetNotBlocked(int $assetId, int $checklistId): void
    {
        $asset = Asset::with('assetType')->find($assetId);
        if (! $asset || $asset->status !== 'fuera_de_servicio') {
            return;
        }

        $isResolvingChecklist = false;
        foreach ($asset->assetType?->metadata_schema ?? [] as $field) {
            foreach ($field['maintenance_rules'] ?? [] as $rule) {
                if (isset($rule['checklist_resolucion_id']) && (int) $rule['checklist_resolucion_id'] === $checklistId) {
                    $isResolvingChecklist = true;
                    break 2;
                }
            }
        }

        if (! $isResolvingChecklist) {
            throw ValidationException::withMessages([
                'asset_id' => 'ACCESO DENEGADO: El equipo se encuentra Fuera de Servicio por Mantenimiento Vencido.',
            ]);
        }
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

    private function applyCommonHistoryFilters($query, Request $request): void
    {
        if ($request->empresa) {
            $query->whereHas('sede', fn ($q) => $q->where('empresa_id', $request->empresa));
        }
        if ($request->sede) {
            $query->where('sede_id', $request->sede);
        }
        if ($request->fecha_inicio) {
            $query->whereDate('completed_at', '>=', $request->fecha_inicio);
        }
        if ($request->fecha_fin) {
            $query->whereDate('completed_at', '<=', $request->fecha_fin);
        }
    }

    private function paginateGroups($allGroups, Request $request, callable $mapper): LengthAwarePaginator
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        $currentPageItems = $allGroups->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $currentPageItems,
            $allGroups->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
        $paginator->appends($request->all());
        $paginator->setCollection($currentPageItems->map($mapper));

        return $paginator;
    }

    private function formatHistoryGroup($group, ?Sede $sede, ?VisitCertificate $visitCertificate, $inspections, array $extra = [], bool $includeAsset = false): array
    {
        $totalCount = $inspections->count();
        $successCount = $inspections->where('result', '!=', 'Fail')->count();
        $failedCount = $inspections->where('result', 'Fail')->count();

        $checklistDetails = $inspections->map(function ($ins) use ($includeAsset) {
            $item = [
                'id' => $ins->id,
                'checklist_title' => $ins->checklist?->title,
                'registry_code' => $ins->checklist?->registry_code,
                'completed_time' => $ins->completed_at ? $ins->completed_at->format('H:i') : '00:00',
                'supervisor_name' => $ins->supervisor_name ?: 'Sin Firmar',
                'result' => $ins->result,
                'has_failed' => $ins->result === 'Fail',
            ];

            if ($includeAsset) {
                $item['asset'] = $ins->asset ? [
                    'id' => $ins->asset->id,
                    'name' => $ins->asset->name,
                    'identifier' => $ins->asset->identifier,
                    'status' => $ins->asset->status,
                ] : null;
            }

            return $item;
        });

        return array_merge([
            'completed_date' => $group->completed_date,
            'completed_date_formatted' => Carbon::parse($group->completed_date)->format('d/m/Y'),
            'sede_id' => $group->sede_id,
            'sede_nombre' => $sede ? $sede->nombre_sede : 'Sede Desconocida',
            'empresa_nombre' => $sede && $sede->empresa ? $sede->empresa->nombre : 'Empresa Desconocida',
            'visit_certificate_id' => $visitCertificate?->id,
            'visit_certificate_status' => $visitCertificate?->status,
            'visit_certificate_code' => $visitCertificate?->code,
            'total_count' => $totalCount,
            'success_count' => $successCount,
            'failed_count' => $failedCount,
            'compliance_percentage' => $totalCount > 0 ? round(($successCount / $totalCount) * 100) : 100,
            'checklists' => $checklistDetails,
        ], $extra);
    }

    private function getScopedEmpresas(User $user)
    {
        return Empresa::with(['sedes' => fn ($q) => $q->orderBy('nombre_sede')])
            ->when($user->consultora_id, fn ($q) => $q->where('consultora_id', $user->consultora_id))
            ->orderBy('nombre')
            ->get();
    }
}
