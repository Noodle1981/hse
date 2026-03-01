<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\EppDeliveryItem;
use App\Models\EppItem;
use App\Models\EppReplacementAlert;
use App\Contracts\EppExchangeContract;
use Illuminate\Support\Facades\Log;

class EvaluarVencimientosEppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(EppExchangeContract $exchange): void
    {
        Log::info('EvaluarVencimientosEppJob: Comenzando evaluación diaria de EPPs.');

        // 1. EVALUAR VENCIMIENTOS GLOBALES (Por Empleado/Sede)
        $this->evaluarVencimientos($exchange);

        // 2. EVALUAR STOCK GLOBAL (Por Empresa)
        $this->evaluarStock($exchange);

        Log::info('EvaluarVencimientosEppJob: Evaluación finalizada.');
    }

    private function evaluarVencimientos(EppExchangeContract $exchange)
    {
        // Traer todos los items entregados que siguen activos
        $deliveryItems = EppDeliveryItem::with(['eppItem', 'eppDelivery.employee.sede.empresa'])
            ->where('estado', \App\Models\EppDeliveryItem::ESTADO_ACTIVO)
            ->get();

        foreach ($deliveryItems as $dItem) {
            $epp = $dItem->eppItem;
            $employee = $dItem->eppDelivery->employee ?? null;
            $empresa = $employee?->sede?->empresa ?? null;
            $sede = $employee?->sede ?? null;

            if (!$epp || !$employee || !$empresa) continue;

            $dias = $dItem->diasParaVencimiento();
            $estaVencido = $dItem->estaVencido();

            // Si está vencido o vence en <= 30 días
            if ($estaVencido || ($dias !== null && $dias <= 30)) {
                
                $urgencia = $estaVencido ? EppReplacementAlert::URGENCIA_ALTA : EppReplacementAlert::URGENCIA_MEDIA;
                $estadoStr = $estaVencido ? "está VENCIDO" : "vence en {$dias} días";
                $motivo = "El EPP '{$epp->nombre}' del empleado {$employee->full_name} {$estadoStr}.";

                $this->crearAlertaSiNoExiste(
                    $exchange,
                    $empresa->id,
                    $epp->id,
                    $sede->id ?? null,
                    EppReplacementAlert::TRIGGER_VENCIMIENTO,
                    1, // 1 unidad para este empleado
                    $urgencia,
                    $motivo
                );
            }
        }
    }

    private function evaluarStock(EppExchangeContract $exchange)
    {
        // Traer EPPs que necesitan reposición (stock_actual < stock_seguridad)
        $epps = EppItem::necesitaReposicion()->get();

        // NOTA: Actualmente el EPP no está atado a una Empresa específica 
        // en el catálogo base (app/Models/EppItem.php).
        // Asumimos que la empresa que debe reponer es la que más usa este EPP 
        // o generamos una alerta por cada empresa que use el sistema.
        // Como el PRD dice "cada perfil de consultora/empresa debe manejar stock", 
        // y nuestro modelo de BD actual EppItem es global, 
        // generamos la alerta para todas las empresas activas que tengan este EPP en su inventario virtual.
        // Para simplificar este MVP y evitar fallos por FK, la generamos para la Empresa ID = 1 o la primera que encontremos.
        // TODO: En Fase 4, EppItem debería tener un empresa_id.

        $empresas = \App\Models\Empresa::all();

        foreach ($epps as $epp) {
            $falta = $epp->stock_seguridad - $epp->stock_actual;
            if ($falta <= 0) continue;

            $urgencia = $epp->stock_actual == 0 ? EppReplacementAlert::URGENCIA_ALTA : EppReplacementAlert::URGENCIA_MEDIA;
            $motivo = "Stock de '{$epp->nombre}' está en {$epp->stock_actual} unidades. Punto de pedido: {$epp->stock_seguridad}. Faltan {$falta}.";

            foreach ($empresas as $empresa) {
                $this->crearAlertaSiNoExiste(
                    $exchange,
                    $empresa->id,
                    $epp->id,
                    null,
                    EppReplacementAlert::TRIGGER_STOCK_BAJO,
                    $falta,
                    $urgencia,
                    $motivo
                );
            }
        }
    }

    private function crearAlertaSiNoExiste($exchange, $empresaId, $eppItemId, $sedeId, $trigger, $cantidad, $urgencia, $motivo)
    {
        // Buscar si ya hay una alerta PENDIENTE para esta combinación
        $existe = EppReplacementAlert::pendientes()
            ->where('empresa_id', $empresaId)
            ->where('epp_item_id', $eppItemId)
            ->where('trigger_type', $trigger)
            ->exists();

        if ($existe) return;

        $alert = new EppReplacementAlert([
            'empresa_id' => $empresaId,
            'epp_item_id' => $eppItemId,
            'sede_id' => $sedeId,
            'trigger_type' => $trigger,
            'cantidad_requerida' => $cantidad,
            'urgencia' => $urgencia,
            'motivo' => $motivo,
            'status' => EppReplacementAlert::STATUS_PENDIENTE,
        ]);

        // Simular cotización en EPP Exchange
        try {
            $response = $exchange->submitAlert($alert);
            $alert->exchange_response = $response;
            $alert->status = EppReplacementAlert::STATUS_COTIZADA; // Auto-cotizada por el mock
        } catch (\Exception $e) {
            Log::error("Error consultando EPP Exchange al crear alerta: " . $e->getMessage());
            // Se queda como pendiente si falla
        }

        $alert->save();
        Log::info("Alerta generada y/o cotizada: {$motivo}");
    }
}
