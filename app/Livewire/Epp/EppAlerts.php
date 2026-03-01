<?php

namespace App\Livewire\Epp;

use Livewire\Component;
use App\Models\EppReplacementAlert;
use Illuminate\Support\Facades\Log;

class EppAlerts extends Component
{
    public $statusFilter = 'pendiente'; // pendiente, cotizada, comprada, cancelada
    public $urgenciaFilter = ''; // alta, media, baja

    public function setStatusFilter($status)
    {
        $this->statusFilter = $status;
    }

    public function setUrgenciaFilter($urgencia)
    {
        $this->urgenciaFilter = $urgencia;
    }

    /**
     * Marca la alerta como "comprada" simulando el webhook de respuesta de EPP Exchange.
     * Al recibir la confirmación de la otra plataforma, se actualiza el stock y se guarda el order ID.
     */
    public function markAsOrdered($alertId)
    {
        $alert = EppReplacementAlert::with(['eppItem', 'empresa'])->find($alertId);
        
        if ($alert && $alert->status !== EppReplacementAlert::STATUS_COMPRADA) {
            
            // 1. Simular la llamada a EPP Exchange y obtener respuesta (Mock)
            $exchangeService = app(\App\Services\MockEppExchangeService::class);
            $confirmacion = $exchangeService->simulateOrderConfirmation($alert);
            
            // 2. Cierre de ciclo: Actualizar Alerta con el ID de Orden de Exchange
            $alert->update([
                'status' => EppReplacementAlert::STATUS_COMPRADA,
                'exchange_order_id' => $confirmacion['exchange_order_id'],
            ]);

            // 3. Incremento del stock físico en el inventario
            if ($alert->eppItem) {
                $alert->eppItem->increment('stock_actual', $alert->cantidad_requerida);
            }

            session()->flash('message', "Pedido confirmado. Se recibieron {$alert->cantidad_requerida} unid. (Ref: {$confirmacion['exchange_order_id']})");
            Log::info("EppAlerts: Webhook Simulado - Alerta {$alertId} cerrada exitosamente con stock actualizado.");
        }
    }

    /**
     * Descarta la alerta (quizás ya había stock no registrado o se difiere la compra).
     */
    public function dismissAlert($alertId)
    {
        $alert = EppReplacementAlert::find($alertId);
        if ($alert) {
            $alert->update(['status' => EppReplacementAlert::STATUS_CANCELADA]);
            session()->flash('message', "Alerta descartada.");
            Log::info("EppAlerts: Alerta {$alertId} CANCELADA por el usuario.");
        }
    }

    public function render()
    {
        // Traemos las alertas base (con sus relaciones)
        $query = EppReplacementAlert::with(['eppItem', 'empresa', 'sede'])
            ->orderByRaw("CASE urgencia WHEN 'alta' THEN 1 WHEN 'media' THEN 2 WHEN 'baja' THEN 3 ELSE 4 END")
            ->latest();

        // Aplicamos el filtro de status o mostramos las pendientes+cotizadas por defecto
        if ($this->statusFilter === 'pendiente') {
            $query->whereIn('status', [EppReplacementAlert::STATUS_PENDIENTE, EppReplacementAlert::STATUS_COTIZADA]);
        } else {
            $query->where('status', $this->statusFilter);
        }

        if ($this->urgenciaFilter) {
            $query->where('urgencia', $this->urgenciaFilter);
        }

        $alerts = $query->get();

        // Contadores para el encabezado (siempre calculados en base a las pendientes/cotizadas)
        $counts = [
            'total' => EppReplacementAlert::whereIn('status', ['pendiente', 'cotizada'])->count(),
            'alta'  => EppReplacementAlert::whereIn('status', ['pendiente', 'cotizada'])->where('urgencia', 'alta')->count(),
            'media' => EppReplacementAlert::whereIn('status', ['pendiente', 'cotizada'])->where('urgencia', 'media')->count(),
            'baja'  => EppReplacementAlert::whereIn('status', ['pendiente', 'cotizada'])->where('urgencia', 'baja')->count(),
        ];

        return view('livewire.epp.epp-alerts', compact('alerts', 'counts'))
            ->layout('components.layouts.app');
    }
}
