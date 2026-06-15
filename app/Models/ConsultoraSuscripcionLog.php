<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultoraSuscripcionLog extends Model
{
    protected $table = 'consultora_suscripcion_logs';

    // Disable updated_at since we only log creation/transitions
    const UPDATED_AT = null;

    protected $fillable = [
        'consultora_id',
        'estado_anterior',
        'estado_nuevo',
        'user_id',
        'created_at',
    ];

    /**
     * Consultora a la que pertenece este log.
     */
    public function consultora()
    {
        return $this->belongsTo(Consultora::class, 'consultora_id');
    }

    /**
     * Usuario que provocó el cambio de estado (habitualmente un súper administrador).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
