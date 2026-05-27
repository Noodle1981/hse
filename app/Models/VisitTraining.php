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
        'attendees',
        'materials_delivered',
        'notes',
    ];

    protected $casts = [
        'is_adhoc' => 'boolean',
        'attendees' => 'array', // [{name, position, signature}]
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
        return $this->training ? $this->training->description : $value;
    }

    public function getDurationMinutesAttribute($value)
    {
        $duration = $this->training ? $this->training->duration : $value;
        
        if ($duration && $duration > 0) {
            return $duration;
        }

        // Si es 0 o null, armar desde la lógica (diferencia de timestamps)
        if ($this->created_at && $this->updated_at) {
            $diff = $this->created_at->diffInMinutes($this->updated_at);
            return $diff > 0 ? $diff : 15; // 15 min de mínimo
        }

        return 15; // Fallback por defecto en lugar de 0
    }

    public function getNotesAttribute($value)
    {
        $notes = $value ?: ($this->training ? $this->training->notes : null);

        if ($notes && str_contains($notes, 'Vínculo automático desde Plan Anual')) {
            return null;
        }

        return $notes;
    }

    public function getAttendeesCountAttribute()
    {
        if ($this->training) {
            return $this->training->attendances()->count();
        }
        return is_array($this->attendees) ? count($this->attendees) : 0;
    }
}
