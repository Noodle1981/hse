<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory;

    protected $fillable = [
        'informe_id',
        'observacion',
        'recomendacion',
        'imagen1',
        'imagen2'
    ];

    public function informe()
    {
        return $this->belongsTo(Informe::class);
    }
}
