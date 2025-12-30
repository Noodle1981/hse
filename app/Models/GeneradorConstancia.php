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
        'fecha_visita',
        'tipo_visita',
        'observacion',
        'firma_responsable',
        'justificacion_firma',
        'date_firma_responsable',
        'firma_instructor',
        'date_firma_instructor',
        'aclaracion_responsable',
        'email_responsable',
        'token_responsable',
        'code',
        'duration'
    ];

    protected $casts = [
        'fecha_visita' => 'date',
        'date_firma_responsable' => 'datetime',
        'date_firma_instructor' => 'datetime',
    ];

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePendingSignature($query)
    {
        return $query->whereNull('firma_responsable');
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('firma_responsable');
    }

    // Polymorphic relationships for activities
    public function inspections()
    {
        return $this->morphedByMany(DynamicInspection::class, 'actividad', 'constancia_actividades');
    }

    public function trainings()
    {
        return $this->morphedByMany(Training::class, 'actividad', 'constancia_actividades');
    }

    public function riskEvaluations()
    {
        return $this->morphedByMany(RiskEvaluation::class, 'actividad', 'constancia_actividades');
    }

    public function eppDeliveries()
    {
        return $this->morphedByMany(EppDelivery::class, 'actividad', 'constancia_actividades');
    }

    public function hallazgosActividades()
    {
        return $this->morphedByMany(Hallazgo::class, 'actividad', 'constancia_actividades');
    }

    // Helper to get all activities
    public function allActivities()
    {
        return [
            'inspections' => $this->inspections,
            'trainings' => $this->trainings,
            'riskEvaluations' => $this->riskEvaluations,
            'eppDeliveries' => $this->eppDeliveries,
            'hallazgos' => $this->hallazgosActividades,
        ];
    }

    /**
     * Hallazgos detectados durante esta visita (OPCIONAL - Fase 2 Hallazgos)
     */
    public function hallazgos()
    {
        return $this->morphMany(Hallazgo::class, 'origen');
    }
}
