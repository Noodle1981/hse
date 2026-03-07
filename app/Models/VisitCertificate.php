<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sede_id',
        'empresa_id',
        'visit_date',
        'start_time',
        'end_time',
        'status', // draft, in_progress, completed, signed
        'general_observations',
        'signature',
        'signature_skip_reason',
        'email_responsable',
        'token_responsable',
        'code',
        'responsible_name',
        'date_firma_responsable',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'date_firma_responsable' => 'datetime',
    ];

    // Relaciones principales
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    // Integraciones con módulos
    public function checklistCompletions()
    {
        return $this->hasMany(VisitChecklistCompletion::class);
    }

    public function trainings()
    {
        return $this->hasMany(VisitTraining::class);
    }


    public function hallazgos()
    {
        return $this->hasMany(Hallazgo::class);
    }

    public function incidentes()
    {
        return $this->hasMany(Incidente::class);
    }

    // Helpers
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'signed' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'draft' => 'Borrador',
            'in_progress' => 'En Progreso',
            'completed' => 'Completada',
            'signed' => 'Firmada',
            default => $this->status,
        };
    }

    public function getNotesAttribute()
    {
        return $this->general_observations;
    }

    public function getResponsibleSignatureAttribute()
    {
        return $this->signature;
    }
}
