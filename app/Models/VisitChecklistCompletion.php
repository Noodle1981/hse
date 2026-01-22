<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitChecklistCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_certificate_id',
        'checklist_assignment_id',
        'is_self_assigned',
        'completed_at',
        'responses',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'is_self_assigned' => 'boolean',
        'responses' => 'array',
    ];

    public function visit()
    {
        return $this->belongsTo(VisitCertificate::class, 'visit_certificate_id');
    }

    public function assignment()
    {
        return $this->belongsTo(ChecklistAssignment::class, 'checklist_assignment_id');
    }

    // Relación opcional con Hallazgos que surgieron de este checklist
    public function hallazgos()
    {
        return $this->hasMany(Hallazgo::class, 'checklist_completion_id');
    }
}
