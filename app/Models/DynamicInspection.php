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
        'completed_at',
        'notes',
        'status',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * Estados posibles de una inspección
     */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_COMPLETED = 'completed';

    /**
     * El checklist que se está ejecutando
     */
    public function checklist(): BelongsTo
    {
        return $this->belongsTo(DynamicChecklist::class, 'dynamic_checklist_id');
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
        ]);
    }
}
