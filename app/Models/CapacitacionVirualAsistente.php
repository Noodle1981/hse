<?php

namespace App\Models;

use Database\Factories\CapacitacionVirualAsistenteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapacitacionVirualAsistente extends Model
{
    /** @use HasFactory<CapacitacionVirualAsistenteFactory> */
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
