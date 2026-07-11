<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitTraining extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_certificate_id',
        'training_id', // NULL si es ad-hoc
        'is_adhoc',
        'title',
        'description',
        'duration_minutes',
        'materials_delivered',
        'notes',
    ];

    protected $casts = [
        'is_adhoc' => 'boolean',
    ];

    protected $appends = [
        'attendees',
    ];

    public function visit()
    {
        return $this->belongsTo(VisitCertificate::class, 'visit_certificate_id');
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    // Accessors unificados
    public function getTitleAttribute($value)
    {
        return $this->training ? $this->training->title : $value;
    }

    public function getDescriptionAttribute($value)
    {
        return ($this->training && $this->training->description) ? $this->training->description : $value;
    }

    public function getDurationMinutesAttribute($value)
    {
        // 1. Si la columna local tiene un valor guardado, usarlo
        if ($value && $value > 0) {
            return $value;
        }

        // 2. Si hay una plantilla vinculada, heredar su duración
        if ($this->training && $this->training->duration && $this->training->duration > 0) {
            return $this->training->duration;
        }

        // 3. Buscar en el plan anual si existe un tiempo programado
        if ($this->training && $this->training->dynamic_checklist_id && $this->visit) {
            $plan = AnnualPlan::where('sede_id', $this->visit->sede_id)
                ->where('checklist_id', $this->training->dynamic_checklist_id)
                ->first();
            if ($plan && $plan->duration_minutes > 0) {
                return $plan->duration_minutes;
            }
        }

        // 4. Fallback de diferencia de tiempo de guardado (solo para ad-hoc)
        if ($this->is_adhoc && $this->created_at && $this->updated_at) {
            $diff = $this->created_at->diffInMinutes($this->updated_at);

            return $diff > 0 ? $diff : 15;
        }

        return 15;
    }

    public function getNotesAttribute($value)
    {
        $notes = $value ?: ($this->training ? $this->training->notes : null);

        if ($notes && str_contains($notes, 'Vínculo automático desde Plan Anual')) {
            return null;
        }

        return $notes;
    }

    public function attendances()
    {
        return $this->hasMany(TrainingAttendance::class, 'visit_training_id');
    }

    public function getAttendeesCountAttribute()
    {
        return $this->attendances()->count();
    }

    public function getAttendeesAttribute()
    {
        return $this->attendances()->get()->map(function ($att) {
            return [
                'id' => $att->id,
                'name' => $att->employee_name,
                'dni' => $att->dni,
                'signature' => $att->signature,
                'timestamp' => $att->created_at ? $att->created_at->toDateTimeString() : null,
            ];
        })->toArray();
    }
}
