<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneradorConstancia extends Model
{
    /** @use HasFactory<\Database\Factories\GeneradorConstanciaFactory> */
    use HasFactory;

    protected $fillable = [
        'sede_id',
        'user_id',
        'tipo_visita',
        'observacion',
        'firma_responsable',
        'date_firma_responsable',
        'aclaracion_responsable',
        'email_responsable',
        'token_responsable',
        'code',
        'duration'
    ];

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }
    
    
}
