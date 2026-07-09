<?php

namespace App\Listeners;

use App\Events\InspectionCompleted;
use Illuminate\Support\Facades\Log;

class SyncAssetMetadata
{
    public function handle(InspectionCompleted $event): void
    {
        $inspection = $event->inspection;
        $inspection->load(['asset', 'answers.question']);

        $asset = $inspection->asset;
        if (! $asset) {
            return;
        }
        $asset->load('assetType');

        $metadata = $asset->metadata ?? [];
        $hasChanges = false;

        foreach ($inspection->answers as $answer) {
            $question = $answer->question;

            if ($question && ! empty($question->vincular_a_metadata)) {
                $metricKey = $question->vincular_a_metadata;
                $val = $answer->value;
                
                $oldVal = isset($metadata[$metricKey]) ? $metadata[$metricKey] : null;
                
                // Normalizar a string para comparar
                $oldValStr = $oldVal !== null ? (is_array($oldVal) ? json_encode($oldVal) : (string)$oldVal) : null;
                $newValStr = $val !== null ? (is_array($val) ? json_encode($val) : (string)$val) : null;

                if ($oldValStr !== $newValStr) {
                    \App\Models\AssetMetadataLog::create([
                        'asset_id' => $asset->id,
                        'dynamic_inspection_id' => $inspection->id,
                        'field_key' => $metricKey,
                        'old_value' => $oldValStr,
                        'new_value' => $newValStr,
                        'changed_by' => $inspection->user_id ?? \Illuminate\Support\Facades\Auth::id(),
                    ]);
                }

                $metadata[$metricKey] = $val;
                $hasChanges = true;

                // Si el esquema define un intervalo recurrente (ej: cada 15.000 km), calculamos el próximo objetivo
                $schema = $asset->assetType?->metadata_schema ?? [];
                foreach ($schema as $field) {
                    if (($field['name'] ?? null) === $metricKey) {
                        // 1. Reglas SRE
                        $rules = $field['maintenance_rules'] ?? [];
                        if (! empty($rules) && is_array($rules)) {
                            foreach ($rules as $rule) {
                                if (isset($rule['checklist_resolucion_id']) && (int)$rule['checklist_resolucion_id'] === (int)$inspection->dynamic_checklist_id) {
                                    $ruleName = $rule['nombre'];
                                    $frecuencia = (float)$rule['frecuencia'];
                                    $nextTargetKey = 'proximo_' . $metricKey . '_' . \Illuminate\Support\Str::slug($ruleName);
                                    
                                    if (($field['type'] ?? 'number') === 'number' && is_numeric($val)) {
                                        $metadata[$nextTargetKey] = (float)$val + $frecuencia;
                                    } elseif (($field['type'] ?? '') === 'date' && !empty($val)) {
                                        try {
                                            $metadata[$nextTargetKey] = \Carbon\Carbon::parse($val)->addDays((int)$frecuencia)->toDateString();
                                        } catch (\Exception $e) {
                                            Log::warning("No se pudo calcular proxima fecha para activo {$asset->id}: ".$e->getMessage());
                                        }
                                    }
                                    
                                    Log::info("SRE: Calculado próximo objetivo de regla '{$ruleName}' para activo {$asset->id}: " . ($metadata[$nextTargetKey] ?? 'error'));

                                    // Auto-resolver la orden de trabajo (ChecklistAssignment)
                                    if ($inspection->checklist_assignment_id) {
                                        \App\Models\ChecklistAssignment::where('id', $inspection->checklist_assignment_id)
                                            ->update(['status' => 'completed']);
                                    } else {
                                        \App\Models\ChecklistAssignment::where('asset_id', $asset->id)
                                            ->where('dynamic_checklist_id', $inspection->dynamic_checklist_id)
                                            ->where('status', '!=', 'completed')
                                            ->update(['status' => 'completed']);
                                    }
                                }
                            }
                        }

                        // 2. Regla antigua simple
                        if (! empty($field['alert_interval']) && is_numeric($val)) {
                            $interval = (float) $field['alert_interval'];
                            $metadata['proximo_' . $metricKey] = (float) $val + $interval;
                            Log::info("Calculado próximo objetivo de {$metricKey} para activo {$asset->id}: " . ($val + $interval));
                        }
                    }
                }
            }
        }

        // B5: Si la inspección falló, cambiar estado a 'fuera_de_servicio' de forma automática
        if ($inspection->result === 'Fail' && $asset->status !== 'fuera_de_servicio') {
            $asset->status = 'fuera_de_servicio';
            $hasChanges = true;

            Log::info("Activo {$asset->id} cambiado automáticamente a 'fuera_de_servicio' por inspección fallida #{$inspection->id}");
        }

        if ($hasChanges) {
            $asset->metadata = $metadata;
            $asset->save();

            // Re-evaluar alertas del activo (disparar o resolver hallazgos según nuevos valores)
            \Illuminate\Support\Facades\Artisan::call('assets:check-alerts');
        }
    }
}
