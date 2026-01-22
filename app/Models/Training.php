<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
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
    ];

    protected $casts = [
        'date' => 'datetime',
        'is_template' => 'boolean',
    ];

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
