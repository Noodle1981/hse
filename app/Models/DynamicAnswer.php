<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DynamicAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'dynamic_inspection_id',
        'dynamic_question_id',
        'value',
    ];

    /**
     * La inspección a la que pertenece esta respuesta
     */
    public function inspection(): BelongsTo
    {
        return $this->belongsTo(DynamicInspection::class, 'dynamic_inspection_id');
    }

    /**
     * La pregunta que se respondió
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(DynamicQuestion::class, 'dynamic_question_id');
    }

    /**
     * Obtener el valor como array (para checkboxes)
     */
    public function getValueAsArray(): array
    {
        $decoded = json_decode($this->value, true);
        return is_array($decoded) ? $decoded : [$this->value];
    }
}
