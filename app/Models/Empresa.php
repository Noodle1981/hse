<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    /** @use HasFactory<\Database\Factories\EmpresaFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'consultora_id',
        'nombre',
        'razon_social',
        'cuit',
        'direccion',
        'email',
        'logo_path',
        'primary_color',
        'branding_config',
    ];

    protected $casts = [
        'branding_config' => 'array',
    ];

    /**
     * Consultora a la que pertenece esta empresa
     */
    public function consultora()
    {
        return $this->belongsTo(Consultora::class, 'consultora_id');
    }

    public function sedes()
    {
        return $this->hasMany(Sede::class);
    }

    public function assignments()
    {
        return $this->hasMany(ChecklistAssignment::class);
    }
}
