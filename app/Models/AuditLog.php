<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    // Disable updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'action',
        'target_type',
        'target_id',
        'payload',
        'created_at',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    /**
     * Súper administrador o vendedor que realizó la acción.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación polimórfica para la entidad objetivo del cambio.
     */
    public function target()
    {
        return $this->morphTo(null, 'target_type', 'target_id');
    }

    protected static function booted()
    {
        // Los registros de auditoría son inmutables por motivos de seguridad
        static::updating(function ($log) {
            throw new \Exception('Los registros de auditoría son inmutables y no se pueden modificar.');
        });

        static::deleting(function ($log) {
            throw new \Exception('Los registros de auditoría son inmutables y no se pueden eliminar.');
        });
    }
}
