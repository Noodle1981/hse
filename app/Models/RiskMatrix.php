<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskMatrix extends Model
{
    protected $fillable = [
        'title',
        'description',
        'area',
        'process',
        'empresa_id',
        'sede_id',
        'user_id',
        'status',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function risks()
    {
        return $this->hasMany(Risk::class)->orderBy('order');
    }
}
