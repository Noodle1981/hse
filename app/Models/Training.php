<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'sede_id',
        'user_id',
        'created_by_user_id', // Creador del training (admin o técnico ad-hoc)
        'is_template', // true=plantilla admin, false=ad-hoc
        'title',
        'description',
        'type',
        'date',
        'duration',
        'trainer_name',
        'status',
        'notes',
        'dynamic_checklist_id',
    ];

    public const STATUS_PENDING = 'pending';

    public const STATUS_COMPLETED = 'completed';

    protected $casts = [
        'date' => 'datetime',
        'is_template' => 'boolean',
    ];

    /**
     * Scope para capacitaciones completadas
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function dynamicChecklist()
    {
        return $this->belongsTo(\App\Models\DynamicChecklist::class, 'dynamic_checklist_id');
    }

    public function responsibleUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by_user_id');
    }

    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class);
    }

    public function sede()
    {
        return $this->belongsTo(\App\Models\Sede::class);
    }

    public function attendances()
    {
        return $this->hasMany(\App\Models\TrainingAttendance::class);
    }

    // Ejecuciones de esta capacitación en visitas
    public function visitTrainings()
    {
        return $this->hasMany(VisitTraining::class);
    }

    public function constancias()
    {
        return $this->morphToMany(GeneradorConstancia::class, 'actividad', 'constancia_actividades');
    }
}
