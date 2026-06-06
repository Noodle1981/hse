<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incidente extends Model
{
    use HasFactory;

    protected $fillable = [
        'hallazgo_id',
        'sede_id',
        'user_id',
        'tipo',
        'gravedad',
        'fecha_ocurrencia',
        'descripcion',
        'personas_afectadas',
        'dias_perdidos',
        'medidas_tomadas',
        'titulo',
        'ubicacion_especifica',
        'estado',
        'visit_certificate_id',
    ];

    protected $casts = [
        'fecha_ocurrencia' => 'datetime',
        'personas_afectadas' => 'integer',
        'dias_perdidos' => 'integer',
    ];

    // ============================================
    // CONSTANTES
    // ============================================

    // Tipos de incidente
    public const TIPO_ACCIDENTE = 'accidente';

    public const TIPO_INCIDENTE = 'incidente';

    public const TIPO_CUASI_ACCIDENTE = 'cuasi_accidente';

    // Niveles de gravedad
    public const GRAVEDAD_LEVE = 'leve';

    public const GRAVEDAD_MODERADO = 'moderado';

    public const GRAVEDAD_GRAVE = 'grave';

    public const GRAVEDAD_FATAL = 'fatal';

    // ============================================
    // RELACIONES
    // ============================================

    /**
     * El hallazgo que causó o está relacionado con este incidente (opcional)
     */
    public function hallazgo(): BelongsTo
    {
        return $this->belongsTo(Hallazgo::class);
    }

    /**
     * La sede donde ocurrió el incidente
     */
    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    /**
     * El usuario que reportó el incidente
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Visita asociada (integración)
     */
    public function visit(): BelongsTo
    {
        return $this->belongsTo(VisitCertificate::class, 'visit_certificate_id');
    }

    /**
     * Fotos del incidente
     */
    public function fotos()
    {
        return $this->hasMany(IncidenteFoto::class);
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Incidentes tipo accidente
     */
    public function scopeAccidentes($query)
    {
        return $query->where('tipo', self::TIPO_ACCIDENTE);
    }

    /**
     * Incidentes tipo incidente
     */
    public function scopeIncidentes($query)
    {
        return $query->where('tipo', self::TIPO_INCIDENTE);
    }

    /**
     * Cuasi accidentes
     */
    public function scopeCuasiAccidentes($query)
    {
        return $query->where('tipo', self::TIPO_CUASI_ACCIDENTE);
    }

    /**
     * Incidentes graves o fatales
     */
    public function scopeGraves($query)
    {
        return $query->whereIn('gravedad', [self::GRAVEDAD_GRAVE, self::GRAVEDAD_FATAL]);
    }

    /**
     * Incidentes con días perdidos
     */
    public function scopeConDiasPerdidos($query)
    {
        return $query->where('dias_perdidos', '>', 0);
    }

    /**
     * Incidentes relacionados con hallazgos
     */
    public function scopeConHallazgo($query)
    {
        return $query->whereNotNull('hallazgo_id');
    }

    /**
     * Incidentes por sede
     */
    public function scopePorSede($query, $sedeId)
    {
        return $query->where('sede_id', $sedeId);
    }

    /**
     * Incidentes en un rango de fechas
     */
    public function scopeEntreFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_ocurrencia', [$desde, $hasta]);
    }

    // ============================================
    // MÉTODOS AUXILIARES
    // ============================================

    /**
     * Verificar si es un accidente
     */
    public function esAccidente(): bool
    {
        return $this->tipo === self::TIPO_ACCIDENTE;
    }

    /**
     * Verificar si es grave o fatal
     */
    public function esGrave(): bool
    {
        return in_array($this->gravedad, [self::GRAVEDAD_GRAVE, self::GRAVEDAD_FATAL]);
    }

    /**
     * Verificar si tuvo días perdidos
     */
    public function tuvoDiasPerdidos(): bool
    {
        return $this->dias_perdidos > 0;
    }

    /**
     * Obtener el color según gravedad
     */
    public function getColorGravedad(): string
    {
        return match ($this->gravedad) {
            self::GRAVEDAD_FATAL => 'red',
            self::GRAVEDAD_GRAVE => 'orange',
            self::GRAVEDAD_MODERADO => 'yellow',
            self::GRAVEDAD_LEVE => 'green',
            default => 'gray',
        };
    }

    /**
     * Obtener el color según tipo
     */
    public function getColorTipo(): string
    {
        return match ($this->tipo) {
            self::TIPO_ACCIDENTE => 'red',
            self::TIPO_INCIDENTE => 'orange',
            self::TIPO_CUASI_ACCIDENTE => 'blue',
            default => 'gray',
        };
    }

    /**
     * Calcular índice de frecuencia (IF)
     * IF = (Número de accidentes * 1,000,000) / Horas trabajadas
     */
    public static function calcularIndiceFrecuencia($numeroAccidentes, $horasTrabajadas): float
    {
        if ($horasTrabajadas == 0) {
            return 0;
        }

        return ($numeroAccidentes * 1000000) / $horasTrabajadas;
    }

    /**
     * Calcular índice de gravedad (IG)
     * IG = (Días perdidos * 1,000,000) / Horas trabajadas
     */
    public static function calcularIndiceGravedad($diasPerdidos, $horasTrabajadas): float
    {
        if ($horasTrabajadas == 0) {
            return 0;
        }

        return ($diasPerdidos * 1000000) / $horasTrabajadas;
    }
}
