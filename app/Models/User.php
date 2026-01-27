<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'consultora_id',
        'nombres',
        'apellidos',
        'email',
        'password',
        'genero',
        'telefono',
        'n_doc',
        'rol',
        'matricula',
        'estado',
        'firma',
        'titulo'
    ];

    /**
     * Consultora a la que pertenece este usuario (Admin/Técnico)
     */
    public function consultora()
    {
        return $this->belongsTo(Consultora::class, 'consultora_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sedes(): BelongsToMany
    {
        return $this->belongsToMany(Sede::class, 'sede_users');
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

    public function registros()
    {
        return $this->hasMany(Registro::class);
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->rol, $roles);
        }
        return $this->rol === $roles;
    }

    /**
     * Verifica si el usuario es Super Administrador (Global)
     */
    public function isSuperAdmin(): bool
    {
        return strtolower($this->rol) === 'super_admin';
    }

    /**
     * Verifica si el usuario es Administrador
     */
    public function isAdmin(): bool
    {
        return in_array(strtolower($this->rol), ['administrador', 'admin', 'super_admin']);
    }

    /**
     * Verifica si el usuario es Técnico
     */
    public function isTechnician(): bool
    {
        return in_array(strtolower($this->rol), ['técnico', 'tecnico']);
    }

    /**
     * Accesor para nombre completo
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->nombres} {$this->apellidos}");
    }
}
