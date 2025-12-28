<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Risk extends Model
{
    protected $fillable = [
        'risk_matrix_id',
        'hazard',
        'type',
        'consequence',
        'order',
    ];

    public function matrix()
    {
        return $this->belongsTo(RiskMatrix::class, 'risk_matrix_id');
    }

    public function evaluations()
    {
        return $this->hasMany(RiskEvaluation::class)->orderByDesc('evaluation_date');
    }

    public function latestEvaluation()
    {
        return $this->hasOne(RiskEvaluation::class)->latestOfMany('evaluation_date');
    }

    public function controls()
    {
        return $this->hasMany(RiskControl::class);
    }
}
