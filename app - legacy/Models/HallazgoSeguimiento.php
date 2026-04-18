<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HallazgoSeguimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'hallazgo_id',
        'user_id',
        'estado_anterior',
        'estado_nuevo',
        'comentario',
        'fecha_seguimiento',
    ];

    protected $casts = [
        'fecha_seguimiento' => 'datetime',
    ];

    // ============================================
    // RELACIONES
    // ============================================

    /**
     * El hallazgo al que pertenece este seguimiento
     */
    public function hallazgo(): BelongsTo
    {
        return $this->belongsTo(Hallazgo::class);
    }

    /**
     * El usuario que realizó el seguimiento
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ============================================
    // MÉTODOS AUXILIARES
    // ============================================

    /**
     * Verificar si hubo cambio de estado
     */
    public function hubocambioEstado(): bool
    {
        return $this->estado_anterior !== $this->estado_nuevo;
    }

    /**
     * Obtener descripción del cambio
     */
    public function getDescripcionCambio(): string
    {
        if (!$this->huboambioEstado()) {
            return "Actualización sin cambio de estado";
        }

        return "Cambió de '{$this->estado_anterior}' a '{$this->estado_nuevo}'";
    }
}

