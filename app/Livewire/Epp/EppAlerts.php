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
     * Marca la alerta como "comprada", simulando que el usuario generó la orden de compra.
     */
    public function markAsOrdered($alertId)
    {
        $alert = EppReplacementAlert::find($alertId);
        if ($alert) {
            $alert->update(['status' => EppReplacementAlert::STATUS_COMPRADA]);
            session()->flash('message', "Pedido de reposición generado para {$alert->eppItem->nombre}.");
            Log::info("EppAlerts: Alerta {$alertId} marcada como COMPRADA por el usuario.");
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
            ->orderByRaw("FIELD(urgencia, 'alta', 'media', 'baja')")
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
            ->layout('layouts.app');
    }
}
