<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskControl extends Model
{
    protected $fillable = [
        'risk_id',
        'measure',
        'type',
        'status',
        'due_date',
        'checklist_id',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function risk()
    {
        return $this->belongsTo(Risk::class);
    }

    public function checklist()
    {
        return $this->belongsTo(DynamicChecklist::class, 'checklist_id');
    }
}
