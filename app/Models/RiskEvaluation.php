<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskEvaluation extends Model
{
    protected $fillable = [
        'risk_id',
        'user_id',
        'probability',
        'severity',
        'score',
        'level',
        'color',
        'justification',
        'evaluation_date',
    ];

    protected $casts = [
        'evaluation_date' => 'datetime',
    ];

    public function risk()
    {
        return $this->belongsTo(Risk::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
