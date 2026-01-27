<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'empresa_id',
        'sede_id',
        'name',
        'last_name',
        'dni',
        'cuil',
        'puesto',
        'is_active',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function deliveries()
    {
        return $this->hasMany(EppDelivery::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->last_name}, {$this->name}";
    }
}
