<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapacitacionVirualAsistente extends Model
{
    /** @use HasFactory<\Database\Factories\CapacitacionVirualAsistenteFactory> */
    use HasFactory;

    protected $fillable = [
        'capacitacion_virtual_id',
        'nombre_apellido',
        'dni',
        'puesto',
        'evaluacion',
    ];

    public function capacitacionVirtual()
    {
        return $this->belongsTo(CapacitacionVirual::class);
    }
}
