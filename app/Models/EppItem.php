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
        'codigo_norma',
        'stock_actual',
        'is_active',
    ];

    public function deliveries()
    {
        return $this->belongsToMany(EppDelivery::class, 'epp_delivery_items')
                    ->withPivot('cantidad', 'tipo_entrega')
                    ->withTimestamps();
    }
}
