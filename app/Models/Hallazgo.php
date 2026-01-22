<?php

namespace App\Models;

use App\Models\Risk;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Hallazgo extends Model
{
    use HasFactory;

    protected $fillable = [
        'sede_id',
        'user_id',
        'titulo',
        'descripcion',
        'ubicacion_especifica',
        'criticidad',
        'estado',
        'plazo_correccion',
        'responsable_correccion',
        'fecha_deteccion',
        'fecha_resolucion',
        'origen_type',
        'origen_id',
        'risk_id',
        'visit_certificate_id',
        'checklist_completion_id',
        'question_id',
    ];

    protected $casts = [
        'fecha_deteccion' => 'datetime',
        'fecha_resolucion' => 'datetime',
        'plazo_correccion' => 'date',
    ];

    // ============================================
    // CONSTANTES
    // ============================================

    // Niveles de criticidad
    public const CRITICIDAD_BAJO = 'bajo';
    public const CRITICIDAD_MEDIO = 'medio';
    public const CRITICIDAD_ALTO = 'alto';
    public const CRITICIDAD_CRITICO = 'critico';

    // Estados del hallazgo
    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_EN_PROCESO = 'en_proceso';
    public const ESTADO_RESUELTO = 'resuelto';
    public const ESTADO_VERIFICADO = 'verificado';
    public const ESTADO_CERRADO = 'cerrado';

    // ============================================
    // RELACIONES
    // ============================================

    /**
     * La sede donde se detectó el hallazgo
     */
    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    /**
     * El usuario (técnico) que detectó el hallazgo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación polimórfica con el origen del hallazgo
     * Puede ser: DynamicInspection, GeneradorConstancia, o Manual
     */
    public function origen(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Riesgo asociado en la matriz de riesgos (opcional)
     */
    public function risk(): BelongsTo
    {
        return $this->belongsTo(Risk::class);
    }

    /**
     * Seguimientos/actualizaciones del hallazgo
     */
    public function seguimientos(): HasMany
    {
        return $this->hasMany(HallazgoSeguimiento::class)->orderBy('fecha_seguimiento', 'desc');
    }

    /**
     * Fotos de evidencia del hallazgo
     */
    public function fotos(): HasMany
    {
        return $this->hasMany(HallazgoFoto::class);
    }

    /**
     * Incidentes relacionados con este hallazgo
     */
    public function incidentes(): HasMany
    {
        return $this->hasMany(Incidente::class);
    }

    /**
     * Visita asociada (integración)
     */
    public function visit(): BelongsTo
    {
        return $this->belongsTo(VisitCertificate::class, 'visit_certificate_id');
    }

    /**
     * Checklist completion asociado
     */
    public function checklistCompletion(): BelongsTo
    {
        return $this->belongsTo(VisitChecklistCompletion::class, 'checklist_completion_id');
    }

    /**
     * Pregunta específica del checklist
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(DynamicQuestion::class, 'question_id');
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Hallazgos pendientes de resolución
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', self::ESTADO_PENDIENTE);
    }

    /**
     * Hallazgos en proceso
     */
    public function scopeEnProceso($query)
    {
        return $query->where('estado', self::ESTADO_EN_PROCESO);
    }

    /**
     * Hallazgos resueltos
     */
    public function scopeResueltos($query)
    {
        return $query->where('estado', self::ESTADO_RESUELTO);
    }

    /**
     * Hallazgos cerrados
     */
    public function scopeCerrados($query)
    {
        return $query->where('estado', self::ESTADO_CERRADO);
    }

    /**
     * Hallazgos críticos
     */
    public function scopeCriticos($query)
    {
        return $query->where('criticidad', self::CRITICIDAD_CRITICO);
    }

    /**
     * Hallazgos de alta criticidad
     */
    public function scopeAltos($query)
    {
        return $query->where('criticidad', self::CRITICIDAD_ALTO);
    }

    /**
     * Hallazgos vencidos (plazo de corrección pasado)
     */
    public function scopeVencidos($query)
    {
        return $query->where('plazo_correccion', '<', now())
            ->whereNotIn('estado', [self::ESTADO_RESUELTO, self::ESTADO_CERRADO]);
    }

    /**
     * Hallazgos abiertos (pendientes o en proceso)
     */
    public function scopeAbiertos($query)
    {
        return $query->whereIn('estado', [self::ESTADO_PENDIENTE, self::ESTADO_EN_PROCESO]);
    }

    /**
     * Hallazgos por sede
     */
    public function scopePorSede($query, $sedeId)
    {
        return $query->where('sede_id', $sedeId);
    }

    /**
     * Hallazgos por técnico
     */
    public function scopePorTecnico($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ============================================
    // MÉTODOS AUXILIARES
    // ============================================

    /**
     * Verificar si el hallazgo está vencido
     */
    public function estaVencido(): bool
    {
        if (!$this->plazo_correccion) {
            return false;
        }

        return $this->plazo_correccion->isPast() 
            && !in_array($this->estado, [self::ESTADO_RESUELTO, self::ESTADO_CERRADO]);
    }

    /**
     * Verificar si el hallazgo está abierto
     */
    public function estaAbierto(): bool
    {
        return in_array($this->estado, [self::ESTADO_PENDIENTE, self::ESTADO_EN_PROCESO]);
    }

    /**
     * Obtener el color según criticidad
     */
    public function getColorCriticidad(): string
    {
        return match($this->criticidad) {
            self::CRITICIDAD_CRITICO => 'red',
            self::CRITICIDAD_ALTO => 'orange',
            self::CRITICIDAD_MEDIO => 'yellow',
            self::CRITICIDAD_BAJO => 'green',
            default => 'gray',
        };
    }

    /**
     * Obtener el color según estado
     */
    public function getColorEstado(): string
    {
        return match($this->estado) {
            self::ESTADO_CERRADO => 'green',
            self::ESTADO_VERIFICADO => 'blue',
            self::ESTADO_RESUELTO => 'cyan',
            self::ESTADO_EN_PROCESO => 'yellow',
            self::ESTADO_PENDIENTE => 'red',
            default => 'gray',
        };
    }

    /**
     * Agregar un seguimiento al hallazgo
     */
    public function agregarSeguimiento(string $estadoNuevo, string $comentario, ?int $userId = null): HallazgoSeguimiento
    {
        $seguimiento = $this->seguimientos()->create([
            'user_id' => $userId ?? auth()->id(),
            'estado_anterior' => $this->estado,
            'estado_nuevo' => $estadoNuevo,
            'comentario' => $comentario,
            'fecha_seguimiento' => now(),
        ]);

        // Actualizar el estado del hallazgo
        $this->update(['estado' => $estadoNuevo]);

        // Si se marca como resuelto, guardar fecha de resolución
        if ($estadoNuevo === self::ESTADO_RESUELTO && !$this->fecha_resolucion) {
            $this->update(['fecha_resolucion' => now()]);
        }

        return $seguimiento;
    }
}
