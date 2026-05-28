<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'sede_id',
        'category',
        'frequency',
        'activity_name',
        'activity_code',
        'checklist_id',
        'period_start',
        'period_end',
        'scheduled_months',
        'scheduled_dates',
        'duration_minutes',
        'modality',
        'multi_trainer',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'scheduled_months' => 'array',
        'scheduled_dates'  => 'array',
        'multi_trainer'    => 'boolean',
    ];

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function checklist()
    {
        return $this->belongsTo(DynamicChecklist::class, 'checklist_id');
    }
}
