<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    /** @use HasFactory<\Database\Factories\EmpresaFactory> */
    use HasFactory;

    protected $fillable = [
        'nombre',
        'razon_social',
        'cuit'
    ];

    public function sedes()
    {
        return $this->hasMany(Sede::class);
    }
}
