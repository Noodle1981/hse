<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultora extends Model
{
    /** @use HasFactory<\Database\Factories\ConsultoraFactory> */
    use HasFactory;

    protected $table = 'consultoras';

    protected $fillable = [
        'nombre',
        'cuit',
        'max_tecnicos',
        'config',
        'description',
        'fiscal_address',
        'province',
        'locality',
        'email',
        'phone',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    /**
     * Empresas gestionadas por esta consultora
     */
    public function empresas()
    {
        return $this->hasMany(Empresa::class, 'consultora_id');
    }

    /**
     * Usuarios (Admin/Técnicos) pertenecientes a esta consultora
     */
    public function users()
    {
        return $this->hasMany(User::class, 'consultora_id');
    }
}
