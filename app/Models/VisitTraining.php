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
        return $this->training ? $this->training->duration : $value;
    }

    public function getAttendeesCountAttribute()
    {
        if ($this->training) {
            return $this->training->attendances()->count();
        }
        return is_array($this->attendees) ? count($this->attendees) : 0;
    }
}
