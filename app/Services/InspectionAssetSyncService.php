<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetMetadataLog;
use App\Models\AssetType;
use App\Models\DynamicInspection;
use App\Models\User;
use App\Models\VisitChecklistCompletion;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InspectionAssetSyncService
{
    public function relinkAsset(User $user, DynamicInspection $inspection, ?int $newAssetId): void
    {
        $oldAsset = $inspection->asset;

        DB::transaction(function () use ($user, $inspection, $oldAsset, $newAssetId) {
            $inspection->update(['asset_id' => $newAssetId]);

            if ($oldAsset) {
                $logs = AssetMetadataLog::where('asset_id', $oldAsset->id)->where('dynamic_inspection_id', $inspection->id)->get();
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
                $oldAsset->update(['metadata' => $oldMetadata]);
            }

            if ($newAssetId) {
                $newAsset = Asset::findOrFail($newAssetId);
                $newMetadata = $newAsset->metadata ?? [];
                $inspection->load('answers.question');

                foreach ($inspection->answers as $answer) {
                    $metricKey = $answer->question?->vincular_a_metadata;
                    if (empty($metricKey)) {
                        continue;
                    }
                    $val = $answer->value;
                    $oldVal = $newMetadata[$metricKey] ?? null;
                    $oldValStr = $oldVal !== null ? (is_array($oldVal) ? json_encode($oldVal) : (string) $oldVal) : null;
                    $newValStr = $val !== null ? (is_array($val) ? json_encode($val) : (string) $val) : null;

                    if ($oldValStr !== $newValStr) {
                        AssetMetadataLog::create([
                            'asset_id' => $newAsset->id, 'dynamic_inspection_id' => $inspection->id,
                            'field_key' => $metricKey, 'old_value' => $oldValStr, 'new_value' => $newValStr, 'changed_by' => $user->id,
                        ]);
                    }
                    $newMetadata[$metricKey] = $val;
                }
                $newAsset->update(['metadata' => $newMetadata]);
            }
            Artisan::call('assets:check-alerts');
        });
    }

    public function getLinkedAssetsForChecklist(int $sedeId, int $checklistId)
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

        return Asset::where('sede_id', $sedeId)->with('assetType')
            ->when(! empty($linkedAssetTypeIds), fn ($q) => $q->whereIn('asset_type_id', $linkedAssetTypeIds))->get();
    }

    public function getInspectedAssetIdsInVisit(int $visitId): array
    {
        $completedAssignmentIds = VisitChecklistCompletion::where('visit_certificate_id', $visitId)->pluck('checklist_assignment_id')->toArray();

        return DynamicInspection::whereIn('checklist_assignment_id', $completedAssignmentIds)
            ->where('status', 'completed')->whereNotNull('asset_id')->pluck('asset_id')->toArray();
    }

    public function assertAssetNotBlocked(int|string $assetId, int $checklistId): void
    {
        $asset = Asset::with('assetType')->find($assetId);
        if (! $asset || $asset->status !== 'fuera_de_servicio') {
            return;
        }

        foreach ($asset->assetType?->metadata_schema ?? [] as $field) {
            foreach ($field['maintenance_rules'] ?? [] as $rule) {
                if (isset($rule['checklist_resolucion_id']) && (int) $rule['checklist_resolucion_id'] === $checklistId) {
                    return;
                }
            }
        }

        throw ValidationException::withMessages([
            'asset_id' => 'ACCESO DENEGADO: El equipo se encuentra Fuera de Servicio por Mantenimiento Vencido.',
        ]);
    }
}
