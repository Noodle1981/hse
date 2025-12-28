<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $fillable = [
        'empresa_id',
        'sede_id',
        'user_id',
        'title',
        'description',
        'type',
        'date',
        'duration',
        'trainer_name',
        'status',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function responsibleUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class);
    }

    public function sede()
    {
        return $this->belongsTo(\App\Models\Sede::class);
    }

    public function attendances()
    {
        return $this->hasMany(\App\Models\TrainingAttendance::class);
    }}
