<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    /** @use HasFactory<\Database\Factories\SedeFactory> */
    use HasFactory;

    protected $fillable = [
        'nombre_sede',
        'direccion',
        'correo',
        'telefono',
        'tipo_sede',
        'localidad',
        'responsable_1_nombre',
        'responsable_1_cargo',
        'responsable_1_email',
        'responsable_2_nombre',
        'responsable_2_cargo',
        'responsable_2_email',
        'responsable_3_nombre',
        'responsable_3_cargo',
        'responsable_3_email',
        'empresa_id'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function generadorConstancias()
    {
        return $this->hasMany(GeneradorConstancia::class);
    }

    public function checklists()
    {
        return $this->hasMany(ChecklistsAsign::class);
    }

    public function capacitacionesPresenciales()
    {
        return $this->hasMany(CapacitacionPresencial::class);
    }

    public function capacitacionesVirtuales()
    {
        return $this->hasMany(CapacitacionVirual::class);
    }
    
    public function informes()
    {
        return $this->hasMany(Informe::class);
    }
}
