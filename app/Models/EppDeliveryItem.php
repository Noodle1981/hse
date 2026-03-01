<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Carbon\Carbon;

class EppDeliveryItem extends Pivot
{
    protected $table = 'epp_delivery_items';

    // Indica que este pivot tiene su propio id y timestamps
    public $incrementing = true;

    protected $fillable = [
        'epp_delivery_id',
        'epp_item_id',
        'cantidad',
        'tipo_entrega',
        // Campos de trazabilidad
        'lote',
        'fecha_fabricacion',
        'fecha_vencimiento_fabricante',
        'estado',
        'motivo_baja',
        'fecha_baja',
    ];

    protected $casts = [
        'fecha_fabricacion'            => 'date',
        'fecha_vencimiento_fabricante' => 'date',
        'fecha_baja'                   => 'datetime',
    ];

    // ============================================
    // CONSTANTES DE ESTADO
    // ============================================

    const ESTADO_ACTIVO       = 'activo';
    const ESTADO_DADO_DE_BAJA = 'dado_de_baja';
    const ESTADO_VENCIDO      = 'vencido';
    const ESTADO_REEMPLAZADO  = 'reemplazado';

    // ============================================
    // RELACIONES
    // ============================================

    public function eppItem(): BelongsTo
    {
        return $this->belongsTo(EppItem::class);
    }

    public function eppDelivery(): BelongsTo
    {
        return $this->belongsTo(EppDelivery::class);
    }

    // ============================================
    // HELPERS DE CICLO DE VIDA
    // ============================================

    /**
     * Calcula si el EPP está vencido, cruzando:
     *   - Vida útil por uso: delivery_date + epp_item.vida_util_meses
     *   - Vida útil de fábrica: fecha_fabricacion + epp_item.vida_util_fabricante_anios
     *   - Vencimiento explícito del fabricante: fecha_vencimiento_fabricante
     */
    public function estaVencido(): bool
    {
        $fechas = [];

        // 1. Vencimiento por uso intenso
        if ($this->eppDelivery && $this->eppItem?->vida_util_meses) {
            $fechas[] = Carbon::parse($this->eppDelivery->delivery_date)
                ->addMonths($this->eppItem->vida_util_meses);
        }

        // 2. Vencimiento de fábrica (por antigüedad de fabricación)
        if ($this->fecha_fabricacion && $this->eppItem?->vida_util_fabricante_anios) {
            $fechas[] = Carbon::parse($this->fecha_fabricacion)
                ->addYears($this->eppItem->vida_util_fabricante_anios);
        }

        // 3. Vencimiento explícito del fabricante (fecha dura)
        if ($this->fecha_vencimiento_fabricante) {
            $fechas[] = Carbon::parse($this->fecha_vencimiento_fabricante);
        }

        if (empty($fechas)) {
            return false;
        }

        // Toma el vencimiento más próximo
        $fechaMasProxima = collect($fechas)->min();

        return now()->greaterThanOrEqualTo($fechaMasProxima);
    }

    /**
     * Días hasta el vencimiento más próximo (negativo = ya venció)
     */
    public function diasParaVencimiento(): ?int
    {
        $fechas = [];

        if ($this->eppDelivery && $this->eppItem?->vida_util_meses) {
            $fechas[] = Carbon::parse($this->eppDelivery->delivery_date)
                ->addMonths($this->eppItem->vida_util_meses);
        }

        if ($this->fecha_fabricacion && $this->eppItem?->vida_util_fabricante_anios) {
            $fechas[] = Carbon::parse($this->fecha_fabricacion)
                ->addYears($this->eppItem->vida_util_fabricante_anios);
        }

        if ($this->fecha_vencimiento_fabricante) {
            $fechas[] = Carbon::parse($this->fecha_vencimiento_fabricante);
        }

        if (empty($fechas)) {
            return null;
        }

        $fechaMasProxima = collect($fechas)->min();

        return (int) now()->diffInDays($fechaMasProxima, false);
    }

    /**
     * Registra la baja del elemento y actualiza el stock interno del EppItem.
     */
    public function darDeBaja(string $motivo): void
    {
        $this->update([
            'estado'      => self::ESTADO_DADO_DE_BAJA,
            'motivo_baja' => $motivo,
            'fecha_baja'  => now(),
        ]);

        // Descontar del stock_actual del catálogo
        if ($this->eppItem) {
            $this->eppItem->decrement('stock_actual', $this->cantidad);
        }
    }
}
