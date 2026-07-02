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

        $metadata = $asset->metadata ?? [];
        $hasChanges = false;

        foreach ($inspection->answers as $answer) {
            $question = $answer->question;

            if ($question && ! empty($question->vincular_a_metadata)) {
                $metricKey = $question->vincular_a_metadata;
                $val = $answer->value;
                $metadata[$metricKey] = $val;
                $hasChanges = true;

                // Si el esquema define un intervalo recurrente (ej: cada 15.000 km), calculamos el próximo objetivo
                $schema = $asset->assetType?->metadata_schema ?? [];
                foreach ($schema as $field) {
                    if (($field['name'] ?? null) === $metricKey && ! empty($field['alert_interval']) && is_numeric($val)) {
                        $interval = (float) $field['alert_interval'];
                        $metadata['proximo_' . $metricKey] = (float) $val + $interval;
                        Log::info("Calculado próximo objetivo de {$metricKey} para activo {$asset->id}: " . ($val + $interval));
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
