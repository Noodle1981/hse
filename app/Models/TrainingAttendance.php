<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingAttendance extends Model
{
    protected $fillable = [
        'training_id',
        'visit_training_id',
        'user_id',
        'employee_name',
        'dni',
        'signature',
    ];

    public function training()
    {
        return $this->belongsTo(\App\Models\Training::class);
    }

    public function visitTraining()
    {
        return $this->belongsTo(\App\Models\VisitTraining::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
