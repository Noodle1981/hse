<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EppDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'user_id',
        'delivery_date',
        'signature',
        'observaciones',
    ];

    protected $casts = [
        'delivery_date' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->belongsToMany(EppItem::class, 'epp_delivery_items')
                    ->using(EppDeliveryItem::class)
                    ->withPivot(
                        'cantidad', 'tipo_entrega',
                        'lote', 'fecha_fabricacion', 'fecha_vencimiento_fabricante',
                        'estado', 'motivo_baja', 'fecha_baja'
                    )
                    ->withTimestamps();
    }

    public function constancias()
    {
        return $this->morphToMany(GeneradorConstancia::class, 'actividad', 'constancia_actividades');
    }
}
