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
        'titulo',
        'must_change_password',
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
            'must_change_password' => 'boolean',
        ];
    }

    public function sedes(): BelongsToMany
    {
        return $this->belongsToMany(Sede::class, 'sede_users');
    }
    
    public function visitCertificates()
    {
        return $this->hasMany(VisitCertificate::class);
    }

    public function checklists()
    {
        return $this->hasMany(ChecklistAssignment::class);
    }

    public function capacitacionesPresenciales()
    {
        return $this->hasMany(CapacitacionPresencial::class);
    }

    public function capacitacionesVirtuales()
    {
        return $this->hasMany(CapacitacionVirual::class);
    }
    
    public function masterReports()
    {
        return $this->hasMany(MasterReport::class);
    }


    // Role Constants
    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_GESTOR = 'gestor';
    public const ROLE_TECNICO = 'tecnico';

    /**
     * Normaliza un nombre de rol a su valor estándar interno.
     */
    private function normalizeRole(string $role): string
    {
        $role = strtolower(trim($role));
        
        if (in_array($role, ['administrador', 'admin', 'gestor', 'administradores', 'socio'])) {
            return self::ROLE_GESTOR;
        }
        
        if (in_array($role, ['técnico', 'tecnico', 'technician', 'técnicos', 'tecnicos'])) {
            return self::ROLE_TECNICO;
        }
        
        return $role;
    }

    /**
     * Verifica si el usuario tiene uno o más roles (soporta nombres viejos y nuevos).
     * Toma en cuenta el rol activo en la sesión si existe.
     */
    public function hasRole(string|array $roles): bool
    {
        // Prioridad: Rol activo en la sesión (Modo Alternancia)
        $sessionRole = session('active_role');
        $userRoleNormalized = $sessionRole ? $this->normalizeRole($sessionRole) : $this->normalizeRole($this->rol ?? '');

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($userRoleNormalized === $this->normalizeRole($role)) {
                    return true;
                }
            }
            return false;
        }
        
        return $userRoleNormalized === $this->normalizeRole($roles);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    public function isGestor(): bool
    {
        return $this->hasRole(self::ROLE_GESTOR);
    }

    public function isSocio(): bool
    {
        return $this->hasRole('socio');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole([self::ROLE_GESTOR, self::ROLE_SUPER_ADMIN]);
    }

    public function isTechnician(): bool
    {
        return $this->hasRole(self::ROLE_TECNICO);
    }

    /**
     * Accesor para nombre completo (usado en vistas como ->name)
     */
    public function getNameAttribute(): string
    {
        return $this->full_name;
    }

    /**
     * Accesor para nombre completo
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->nombres} {$this->apellidos}");
    }
}
