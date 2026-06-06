<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $appends = ['full_name', 'name'];

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
        'firma_temp',
        'firma_status',
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
     * Parsea recursivamente y aplana cualquier representación de roles a un array plano de strings.
     * Soporta strings simples, separados por comas, pipes (|), y arrays (incluso anidados).
     */
    private function parseRoles(string|array $roles): array
    {
        $parsed = [];
        $items = is_array($roles) ? $roles : [$roles];

        foreach ($items as $item) {
            if (is_string($item)) {
                $parts = preg_split('/[,|]/', $item);
                foreach ($parts as $part) {
                    $trimmed = trim($part);
                    if ($trimmed !== '') {
                        $parsed[] = $trimmed;
                    }
                }
            } elseif (is_array($item)) {
                $parsed = array_merge($parsed, $this->parseRoles($item));
            }
        }

        return $parsed;
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

        \Log::info('hasRole called', ['user' => $this->email, 'roles' => $roles, 'user_role' => $userRoleNormalized]);

        $flatRoles = $this->parseRoles($roles);

        foreach ($flatRoles as $role) {
            if ($userRoleNormalized === $this->normalizeRole($role)) {
                return true;
            }
        }

        return false;
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

    /**
     * Accesor para signature (mapea a la columna firma de la base de datos)
     */
    public function getSignatureAttribute(): ?string
    {
        return $this->firma;
    }
}
