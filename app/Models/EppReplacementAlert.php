<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EppItem;
use App\Models\Empresa;
use App\Models\Sede;

class EppReplacementAlert extends Model
{
    use HasFactory;

    // Tipos de Trigger
    public const TRIGGER_VENCIMIENTO = 'vencimiento_vida_util';
    public const TRIGGER_STOCK_BAJO  = 'stock_bajo';
    public const TRIGGER_CRITICO     = 'evento_critico';

    // Niveles de Urgencia
    public const URGENCIA_ALTA  = 'alta';
    public const URGENCIA_MEDIA = 'media';
    public const URGENCIA_BAJA  = 'baja';

    // Estados
    public const STATUS_PENDIENTE = 'pendiente';
    public const STATUS_COTIZADA  = 'cotizada';
    public const STATUS_COMPRADA  = 'comprada';
    public const STATUS_CANCELADA = 'cancelada';

    protected $fillable = [
        'epp_item_id',
        'empresa_id',
        'sede_id',
        'trigger_type',
        'cantidad_requerida',
        'urgencia',
        'motivo',
        'status',
        'exchange_response',
    ];

    protected $casts = [
        'exchange_response' => 'array',
    ];

    /**
     * Scope para obtener alertas sin resolver
     */
    public function scopePendientes($query)
    {
        return $query->where('status', self::STATUS_PENDIENTE);
    }

    /**
     * Relación con el elemento del catálogo de EPP
     */
    public function eppItem()
    {
        return $this->belongsTo(EppItem::class);
    }

    /**
     * Relación con la Empresa real en la DB
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación opcional con la Sede
     */
    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }
}
