<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EppItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'vida_util_meses',
        'vida_util_fabricante_anios',
        'unidad_medida',
        'codigo_norma',
        'stock_actual',
        'stock_seguridad',
        'requiere_control_critico',
        'is_active',
    ];

    protected $casts = [
        'requiere_control_critico' => 'boolean',
        'is_active'                => 'boolean',
    ];

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Items donde el stock_actual está por debajo del punto de pedido (stock_seguridad).
     * Estos son candidatos para solicitar reposición a EPP Exchange.
     */
    public function scopeNecesitaReposicion($query)
    {
        return $query->whereColumn('stock_actual', '<', 'stock_seguridad');
    }

    // ============================================
    // RELACIONES
    // ============================================

    public function deliveries()
    {
        return $this->belongsToMany(EppDelivery::class, 'epp_delivery_items')
                    ->using(EppDeliveryItem::class)
                    ->withPivot(
                        'cantidad', 'tipo_entrega',
                        'lote', 'fecha_fabricacion', 'fecha_vencimiento_fabricante',
                        'estado', 'motivo_baja', 'fecha_baja'
                    )
                    ->withTimestamps();
    }
}
