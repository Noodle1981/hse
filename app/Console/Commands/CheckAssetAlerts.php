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

        $assets = Asset::with('assetType')->get();

        $alertsCreated = 0;

        foreach ($assets as $asset) {
            $schema = $asset->assetType?->metadata_schema ?? [];
            $metadata = $asset->metadata ?? [];

            if (empty($schema) || empty($metadata)) {
                continue;
            }

            $hasActiveAlert = false;
            $hasCriticalSreAlert = false;
            $hasWarningSreAlert = false;

            foreach ($schema as $field) {
                $fieldName = $field['name'] ?? null;
                $fieldType = $field['type'] ?? 'text';
                if (! $fieldName || ! isset($metadata[$fieldName])) {
                    continue;
                }

                $val = $metadata[$fieldName];
                $fieldLabel = $field['label'] ?? $fieldName;

                // 1. EVALUAR REGLAS SRE (Nuevas reglas múltiples)
                $rules = $field['maintenance_rules'] ?? [];
                if (! empty($rules) && is_array($rules)) {
                    foreach ($rules as $rule) {
                        $ruleName = $rule['nombre'] ?? '';
                        $frecuencia = isset($rule['frecuencia']) ? (float)$rule['frecuencia'] : null;
                        $margen = isset($rule['margen_pre_alerta']) && is_numeric($rule['margen_pre_alerta']) ? (float)$rule['margen_pre_alerta'] : 0;
                        $checklistResolucionId = $rule['checklist_resolucion_id'] ?? null;

                        if (!$ruleName || !$frecuencia || !$checklistResolucionId) {
                            continue;
                        }

                        $isCritico = false;
                        $isPreAlerta = false;
                        $alertReason = '';

                        if ($fieldType === 'number' && is_numeric($val)) {
                            $numVal = (float)$val;
                            $nextTargetKey = 'proximo_' . $fieldName . '_' . \Illuminate\Support\Str::slug($ruleName);
                            $targetVal = isset($metadata[$nextTargetKey]) && is_numeric($metadata[$nextTargetKey])
                                ? (float)$metadata[$nextTargetKey]
                                : $frecuencia;

                            if ($numVal >= $targetVal) {
                                $isCritico = true;
                                $alertReason = "Métrica '{$fieldLabel}' ({$numVal}) alcanzó/superó el límite crítico de {$targetVal} para la regla SRE '{$ruleName}'";
                            } elseif ($numVal >= ($targetVal - $margen)) {
                                $isPreAlerta = true;
                                $alertReason = "Métrica '{$fieldLabel}' ({$numVal}) está próxima al límite crítico de {$targetVal} (Margen: {$margen}) para la regla SRE '{$ruleName}'";
                            }
                        } elseif ($fieldType === 'date' && !empty($val)) {
                            try {
                                $dueDate = Carbon::parse($val);
                                $daysRemaining = (int) now()->startOfDay()->diffInDays($dueDate->startOfDay(), false);

                                if ($daysRemaining <= 0) {
                                    $isCritico = true;
                                    $alertReason = "Fecha '{$fieldLabel}' venció hace " . abs($daysRemaining) . " días (límite: 0 días) para la regla SRE '{$ruleName}'";
                                } elseif ($daysRemaining <= $margen) {
                                    $isPreAlerta = true;
                                    $alertReason = "Fecha '{$fieldLabel}' vence en {$daysRemaining} días (margen alerta: {$margen} días) para la regla SRE '{$ruleName}'";
                                }
                            } catch (\Exception $e) {
                                Log::warning("No se pudo parsear fecha '{$val}' para regla SRE de activo {$asset->id}: ".$e->getMessage());
                            }
                        }

                        if ($isCritico) {
                            $hasCriticalSreAlert = true;
                            $title = "SRE Alerta Crítica: {$asset->name} - {$ruleName}";
                            $this->createFindingAndAssignment($asset, $rule, $title, $alertReason);
                            $alertsCreated++;
                        } elseif ($isPreAlerta) {
                            $hasWarningSreAlert = true;
                            $title = "SRE Alerta Crítica: {$asset->name} - {$ruleName}";
                            $this->resolveFindingAndAssignment($asset, $rule, $title);
                        } else {
                            $title = "SRE Alerta Crítica: {$asset->name} - {$ruleName}";
                            $this->resolveFindingAndAssignment($asset, $rule, $title);
                        }
                    }
                }

                // 2. EVALUAR REGLAS ANTIGUAS/SIMPLES
                $shouldAlert = false;
                $alertReason = '';

                if ($fieldType === 'date' && ! empty($field['alert_days']) && ! empty($val) && empty($rules)) {
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

                if ($fieldType === 'number' && is_numeric($val) && empty($rules)) {
                    $numVal = (float) $val;

                    if (isset($field['alert_threshold']) && is_numeric($field['alert_threshold'])) {
                        $threshold = (float) $field['alert_threshold'];
                        if ($numVal >= $threshold) {
                            $shouldAlert = true;
                            $alertReason = "Métrica '".($field['label'] ?? $fieldName)."' ({$numVal}) superó el umbral configurado de {$threshold}";
                        }
                    }

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
                    $hasActiveAlert = true;
                    $customMsg  = ! empty($field['alert_message']) ? trim($field['alert_message']) : null;
                    
                    $title = "Alerta: {$asset->name} ({$asset->identifier}) - {$fieldName}";
                    $sedeNombre    = $asset->sede?->nombre ?? 'Sede General';
                    $empresaNombre = $asset->empresa?->nombre ?? 'Empresa';

                    if ($customMsg) {
                        $fullDescription = "{$customMsg}\n\n[Detalles del Activo]\n• Activo: {$asset->name} (Código/QR: {$asset->identifier})\n• Ubicación: Sede {$sedeNombre} ({$empresaNombre})\n• Métrica / Condición: {$alertReason}";
                    } else {
                        $fullDescription = "{$alertReason}\n\n[Detalles del Activo]\n• Activo: {$asset->name} (Código/QR: {$asset->identifier})\n• Ubicación: Sede {$sedeNombre} ({$empresaNombre})";
                    }

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
                    }
                } else {
                    if (empty($rules)) {
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

            // Sincronizar el estado del activo en base a las alertas encontradas (prioridad crítico SRE > advertencia SRE > alerta antigua)
            if ($hasCriticalSreAlert) {
                if ($asset->status !== 'fuera_de_servicio') {
                    $asset->update(['status' => 'fuera_de_servicio']);
                    $this->line("Activo '{$asset->name}' ({$asset->identifier}) marcado FUERA DE SERVICIO.");
                }
            } elseif ($hasWarningSreAlert || $hasActiveAlert) {
                if ($asset->status === 'operativo') {
                    $asset->update(['status' => 'alerta']);
                    $this->line("Activo '{$asset->name}' ({$asset->identifier}) marcado en estado ALERTA.");
                }
            } else {
                if ($asset->status === 'alerta') {
                    $asset->update(['status' => 'operativo']);
                    $this->line("Activo '{$asset->name}' ({$asset->identifier}) restablecido a estado OPERATIVO.");
                } elseif ($asset->status === 'fuera_de_servicio') {
                    $hasSreRules = false;
                    foreach ($schema as $f) {
                        if (!empty($f['maintenance_rules'])) {
                            $hasSreRules = true;
                            break;
                        }
                    }
                    if ($hasSreRules) {
                        $asset->update(['status' => 'operativo']);
                        $this->line("Activo '{$asset->name}' ({$asset->identifier}) restablecido a estado OPERATIVO por SRE.");
                    }
                }
            }
        }

        $this->info("Verificación finalizada. {$alertsCreated} hallazgo(s) generado(s).");

        return self::SUCCESS;
    }

    private function createFindingAndAssignment(Asset $asset, array $rule, string $title, string $alertReason)
    {
        $ruleName = $rule['nombre'];
        $checklistResolucionId = $rule['checklist_resolucion_id'];

        $sedeNombre    = $asset->sede?->nombre ?? 'Sede General';
        $empresaNombre = $asset->empresa?->nombre ?? 'Empresa';

        $fullDescription = "Mantenimiento Crítico SRE Requerido para {$ruleName}.\n\n[Detalles del Activo]\n• Activo: {$asset->name} (Código/QR: {$asset->identifier})\n• Ubicación: Sede {$sedeNombre} ({$empresaNombre})\n• Razón: {$alertReason}";

        $existingFinding = Hallazgo::where('origen_type', Asset::class)
            ->where('origen_id', $asset->id)
            ->where('titulo', $title)
            ->whereIn('estado', [Hallazgo::ESTADO_PENDIENTE, Hallazgo::ESTADO_EN_PROCESO])
            ->exists();

        $systemUserId = \Illuminate\Support\Facades\Auth::id()
            ?? $asset->sede?->users()->first()?->id
            ?? \App\Models\User::first()?->id
            ?? 1;

        if (!$existingFinding) {
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
            $this->line("Hallazgo SRE creado para activo {$asset->name}: {$ruleName}");
        }

        $existingAssignment = \App\Models\ChecklistAssignment::where('asset_id', $asset->id)
            ->where('dynamic_checklist_id', $checklistResolucionId)
            ->where('status', '!=', 'completed')
            ->exists();
        if (!$existingAssignment) {
            \App\Models\ChecklistAssignment::create([
                'dynamic_checklist_id' => $checklistResolucionId,
                'asset_id' => $asset->id,
                'user_id' => null,
                'sede_id' => $asset->sede_id,
                'empresa_id' => $asset->empresa_id,
                'status' => 'pending',
                'notes' => "Orden de trabajo SRE autogenerada por alerta crítica en regla: {$ruleName}",
                'due_date' => now(),
            ]);
            $this->line("Orden de trabajo SRE asignada para activo {$asset->name}: Checklist ID {$checklistResolucionId}");
        }
    }

    private function resolveFindingAndAssignment(Asset $asset, array $rule, string $title)
    {
        $checklistResolucionId = $rule['checklist_resolucion_id'];

        Hallazgo::where('origen_type', Asset::class)
            ->where('origen_id', $asset->id)
            ->where('titulo', $title)
            ->whereIn('estado', [Hallazgo::ESTADO_PENDIENTE, Hallazgo::ESTADO_EN_PROCESO])
            ->update([
                'estado' => Hallazgo::ESTADO_RESUELTO,
                'fecha_resolucion' => now(),
            ]);

        \App\Models\ChecklistAssignment::where('asset_id', $asset->id)
            ->where('dynamic_checklist_id', $checklistResolucionId)
            ->where('status', '!=', 'completed')
            ->update([
                'status' => 'completed',
            ]);
    }
}
