<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Informe extends Model
{
    /** @use HasFactory<\Database\Factories\InformeFactory> */
    use HasFactory;

    protected $fillable = [
        'sede_id',
        'user_id',
        'titulo',
        'descripcion',
        'capacitacion_asistentes', 
        'capacitacion_temas',
        'fecha',
    ];

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
