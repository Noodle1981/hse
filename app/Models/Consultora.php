<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultora extends Model
{
    /** @use HasFactory<\Database\Factories\ConsultoraFactory> */
    use HasFactory;

    protected $table = 'consultoras';

    protected $fillable = [
        'nombre',
        'cuit',
        'max_tecnicos',
        'max_gestores',
        'max_empresas',
        'config',
        'description',
        'fiscal_address',
        'province',
        'locality',
        'estado_suscripcion',
        'email',
        'phone',
        'logo_path',
        'color_primary',
        'color_secondary',
        'color_accent',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    /**
     * Empresas gestionadas por esta consultora
     */
    public function empresas()
    {
        return $this->hasMany(Empresa::class, 'consultora_id');
    }

    /**
     * Usuarios (Admin/Técnicos) pertenecientes a esta consultora
     */
    public function users()
    {
        return $this->hasMany(User::class, 'consultora_id');
    }

    /**
     * Historial de trazabilidad de cambios de suscripción
     */
    public function suscripcionLogs()
    {
        return $this->hasMany(ConsultoraSuscripcionLog::class, 'consultora_id');
    }

    protected static function booted()
    {
        static::created(function ($consultant) {
            $consultant->suscripcionLogs()->create([
                'estado_anterior' => null,
                'estado_nuevo' => $consultant->estado_suscripcion ?? 'activa',
                'user_id' => auth()->id(),
            ]);

            $payload = [
                'estado_suscripcion' => ['new' => $consultant->estado_suscripcion ?? 'activa'],
                'max_tecnicos' => ['new' => $consultant->max_tecnicos],
                'max_gestores' => ['new' => $consultant->max_gestores],
                'max_empresas' => ['new' => $consultant->max_empresas],
            ];
            event(new \App\Events\ClientAccessModified($consultant, 'create', $payload));
        });

        static::updating(function ($consultant) {
            if ($consultant->isDirty('estado_suscripcion')) {
                $consultant->suscripcionLogs()->create([
                    'estado_anterior' => $consultant->getOriginal('estado_suscripcion'),
                    'estado_nuevo' => $consultant->estado_suscripcion,
                    'user_id' => auth()->id(),
                ]);
            }

            $changes = [];
            $monitored = ['estado_suscripcion', 'max_tecnicos', 'max_gestores', 'max_empresas'];
            foreach ($monitored as $field) {
                if ($consultant->isDirty($field)) {
                    $changes[$field] = [
                        'old' => $consultant->getOriginal($field),
                        'new' => $consultant->$field,
                    ];
                }
            }
            if (!empty($changes)) {
                event(new \App\Events\ClientAccessModified($consultant, 'update', $changes));
            }
        });
    }
}
