<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DynamicQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'dynamic_checklist_id',
        'section_id',      // Nueva relación con sección (nullable)
        'label',
        'type',
        'options',
        'min_value',
        'max_value',
        'failure_values',
        'failure_logic',   // Nueva lógica de falla (fail_if_selected vs fail_if_missing)
        'order',
        'is_required',
        'help_text',
    ];

    public const LOGIC_FAIL_IF_SELECTED = 'fail_if_selected';

    public const LOGIC_FAIL_IF_MISSING = 'fail_if_missing';

    protected $casts = [
        'options' => 'array',
        'failure_values' => 'array',
        'is_required' => 'boolean',
        'min_value' => 'decimal:2',
        'max_value' => 'decimal:2',
    ];

    /**
     * Tipos de pregunta disponibles
     */
    public const TYPES = [
        'text',
        'textarea',
        'select',
        'radio',
        'checkbox',
        'number',
        'date',
        'signature',
    ];

    /**
     * El checklist al que pertenece esta pregunta
     */
    public function checklist(): BelongsTo
    {
        return $this->belongsTo(DynamicChecklist::class, 'dynamic_checklist_id');
    }

    /**
     * Las respuestas a esta pregunta
     */
    public function answers(): HasMany
    {
        return $this->hasMany(DynamicAnswer::class);
    }

    /**
     * La sección a la que pertenece esta pregunta (opcional)
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(ChecklistSection::class, 'section_id');
    }

    /**
     * Verificar si la pregunta es de tipo opción múltiple
     */
    public function hasOptions(): bool
    {
        return in_array($this->type, ['select', 'radio', 'checkbox']);
    }
}
