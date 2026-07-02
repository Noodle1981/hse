<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\Hallazgo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckAssetAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:check-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Evalúa las alertas de vencimiento y métricas registradas en los activos y genera hallazgos automáticos.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Iniciando verificación de alertas de activos...');

        $assets = Asset::with('assetType')
            ->where('status', '!=', 'fuera_de_servicio')
            ->get();

        $alertsCreated = 0;

        foreach ($assets as $asset) {
            $schema = $asset->assetType?->metadata_schema ?? [];
            $metadata = $asset->metadata ?? [];

            if (empty($schema) || empty($metadata)) {
                continue;
            }

            foreach ($schema as $field) {
                $fieldName = $field['name'] ?? null;
                $fieldType = $field['type'] ?? 'text';
                if (! $fieldName || ! isset($metadata[$fieldName])) {
                    continue;
                }

                $val = $metadata[$fieldName];
                $shouldAlert = false;
                $alertReason = '';

                // Regla para Tipo Fecha (vencimientos)
                if ($fieldType === 'date' && ! empty($field['alert_days']) && ! empty($val)) {
                    try {
                        $dueDate = Carbon::parse($val);
                        $daysRemaining = (int) now()->startOfDay()->diffInDays($dueDate->startOfDay(), false);
                        $alertDays = (int) $field['alert_days'];

                        if ($daysRemaining <= $alertDays) {
                            $shouldAlert = true;
                            $alertReason = "Fecha '".($field['label'] ?? $fieldName)."' vence en {$daysRemaining} días (límite alerta: {$alertDays} días). Valor: {$val}";
                        }
                    } catch (\Exception $e) {
                        Log::warning("No se pudo parsear fecha '{$val}' para el activo {$asset->id}: ".$e->getMessage());
                    }
                }

                // Regla para Tipo Número (umbrales fijos o intervalos recurrentes de kilometraje/horas)
                if ($fieldType === 'number' && is_numeric($val)) {
                    $numVal = (float) $val;

                    // 1. Umbral Fijo
                    if (isset($field['alert_threshold']) && is_numeric($field['alert_threshold'])) {
                        $threshold = (float) $field['alert_threshold'];
                        if ($numVal >= $threshold) {
                            $shouldAlert = true;
                            $alertReason = "Métrica '".($field['label'] ?? $fieldName)."' ({$numVal}) superó el umbral configurado de {$threshold}";
                        }
                    }

                    // 2. Intervalo Recurrente (ej: cada 15.000 km)
                    if (! empty($field['alert_interval']) && is_numeric($field['alert_interval'])) {
                        $nextTargetKey = 'proximo_' . $fieldName;
                        $targetKm = isset($metadata[$nextTargetKey]) && is_numeric($metadata[$nextTargetKey])
                            ? (float) $metadata[$nextTargetKey]
                            : (float) $field['alert_interval'];

                        if ($numVal >= $targetKm) {
                            $shouldAlert = true;
                            $alertReason = "Métrica '".($field['label'] ?? $fieldName)."' ({$numVal}) alcanzó el objetivo de servicio programado ({$targetKm})";
                        }
                    }
                }

                if ($shouldAlert) {
                    $fieldLabel = $field['label'] ?? $fieldName;
                    $customMsg  = ! empty($field['alert_message']) ? trim($field['alert_message']) : null;
                    
                    $title = "Alerta: {$asset->name} ({$asset->identifier}) - {$fieldName}";

                    $sedeNombre    = $asset->sede?->nombre ?? 'Sede General';
                    $empresaNombre = $asset->empresa?->nombre ?? 'Empresa';

                    if ($customMsg) {
                        $fullDescription = "{$customMsg}\n\n[Detalles del Activo]\n• Activo: {$asset->name} (Código/QR: {$asset->identifier})\n• Ubicación: Sede {$sedeNombre} ({$empresaNombre})\n• Métrica / Condición: {$alertReason}";
                    } else {
                        $fullDescription = "{$alertReason}\n\n[Detalles del Activo]\n• Activo: {$asset->name} (Código/QR: {$asset->identifier})\n• Ubicación: Sede {$sedeNombre} ({$empresaNombre})";
                    }

                    // Evitar duplicados: verificar si ya existe un hallazgo pendiente o en proceso para este activo y título
                    $existing = Hallazgo::where('origen_type', Asset::class)
                        ->where('origen_id', $asset->id)
                        ->where('titulo', $title)
                        ->whereIn('estado', [Hallazgo::ESTADO_PENDIENTE, Hallazgo::ESTADO_EN_PROCESO])
                        ->exists();

                    if (! $existing) {
                        $systemUserId = \Illuminate\Support\Facades\Auth::id()
                            ?? $asset->sede?->users()->first()?->id
                            ?? \App\Models\User::first()?->id
                            ?? 1;

                        Hallazgo::create([
                            'sede_id' => $asset->sede_id,
                            'user_id' => $systemUserId,
                            'titulo' => $title,
                            'descripcion' => $fullDescription,
                            'criticidad' => Hallazgo::CRITICIDAD_ALTO,
                            'estado' => Hallazgo::ESTADO_PENDIENTE,
                            'fecha_deteccion' => now(),
                            'origen_type' => Asset::class,
                            'origen_id' => $asset->id,
                        ]);

                        $alertsCreated++;
                        $this->line("Hallazgo creado para activo {$asset->name}: {$fullDescription}");
                    }
                } else {
                    // Auto-resolver hallazgos pendientes si el activo renovó su fecha o corrigió su métrica
                    $title = "Alerta: {$asset->name} ({$asset->identifier}) - {$fieldName}";
                    Hallazgo::where('origen_type', Asset::class)
                        ->where('origen_id', $asset->id)
                        ->where('titulo', $title)
                        ->whereIn('estado', [Hallazgo::ESTADO_PENDIENTE, Hallazgo::ESTADO_EN_PROCESO])
                        ->update([
                            'estado' => Hallazgo::ESTADO_RESUELTO,
                            'fecha_resolucion' => now(),
                        ]);
                }
            }
        }

        $this->info("Verificación finalizada. {$alertsCreated} hallazgo(s) generado(s).");

        return self::SUCCESS;
    }
}
