<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DynamicInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'dynamic_checklist_id',
        'user_id',
        'asset_id',
        'sede_id',
        'checklist_assignment_id',
        'completed_at',
        'notes',
        'status',
        'result',
        'supervisor_name',
        'supervisor_signature',
        'technician_signature',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * El activo asociado a esta inspección (si aplica)
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class)->withTrashed();
    }

    /**
     * Estados posibles de una inspección
     */
    public const STATUS_DRAFT = 'draft';

    public const STATUS_COMPLETED = 'completed';

    /**
     * Resultados posibles de una inspección
     */
    public const RESULT_PASS = 'Pass';

    public const RESULT_FAIL = 'Fail';

    /**
     * El checklist que se está ejecutando
     */
    public function checklist(): BelongsTo
    {
        return $this->belongsTo(DynamicChecklist::class, 'dynamic_checklist_id');
    }

    /**
     * La sede donde se realiza la inspección
     */
    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    /**
     * La asignación que originó esta inspección (si aplica)
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(ChecklistAssignment::class, 'checklist_assignment_id');
    }

    /**
     * El usuario que completó la inspección
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Las respuestas de esta inspección
     */
    public function answers(): HasMany
    {
        return $this->hasMany(DynamicAnswer::class);
    }

    /**
     * Scope para inspecciones completadas
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope para borradores
     */
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Marcar como completada
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
            'result' => $this->evaluateResult(),
        ]);
    }

    /**
     * Evaluar el resultado de la inspección basado en las respuestas y la lógica de las preguntas
     */
    public function evaluateResult(): string
    {
        $this->load(['answers.question']);

        foreach ($this->answers as $answer) {
            if ($answer->isFailure()) {
                return self::RESULT_FAIL;
            }
        }

        return self::RESULT_PASS;
    }

    /**
     * Constancias de visita asociadas
     */
    public function constancias()
    {
        return $this->morphToMany(GeneradorConstancia::class, 'actividad', 'constancia_actividades');
    }

    /**
     * Hallazgos detectados durante esta inspección (OPCIONAL - Fase 2 Hallazgos)
     */
    public function hallazgos()
    {
        return $this->morphMany(Hallazgo::class, 'origen');
    }
}
