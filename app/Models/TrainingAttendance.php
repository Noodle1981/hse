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
        return $this->belongsTo(Training::class);
    }

    public function visitTraining()
    {
        return $this->belongsTo(VisitTraining::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
