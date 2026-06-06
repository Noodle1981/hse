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

    /**
     * Determinar si esta respuesta específica constituye un fallo basandose en la lógica de la pregunta
     */
    public function isFailure(): bool
    {
        $question = $this->question;
        if (! $question) {
            return false;
        }

        $value = $this->value;
        $decodedValue = json_decode($value, true);
        $processedValue = (json_last_error() === JSON_ERROR_NONE) ? $decodedValue : $value;

        // 1. Chequear disparadores de falla (failure_values)
        if (! empty($question->failure_values)) {
            $logic = $question->failure_logic ?? DynamicQuestion::LOGIC_FAIL_IF_SELECTED;

            if ($logic === DynamicQuestion::LOGIC_FAIL_IF_MISSING) {
                // Falla si alguno de los valores requeridos NO está en la respuesta
                if (is_array($processedValue)) {
                    foreach ($question->failure_values as $required) {
                        if (! in_array($required, $processedValue)) {
                            return true;
                        }
                    }
                } else {
                    // Si no es un array (checkbox), y hay valores requeridos, es un fallo a menos que coincida
                    if (! in_array($processedValue, $question->failure_values)) {
                        return true;
                    }
                }
            } else {
                // Lógica estándar: Falla si alguno de los valores seleccionados está marcado como falla
                if (is_array($processedValue)) {
                    foreach ($processedValue as $val) {
                        if (in_array($val, $question->failure_values)) {
                            return true;
                        }
                    }
                } else {
                    if (in_array($processedValue, $question->failure_values)) {
                        return true;
                    }
                }
            }
        }

        // 2. Chequear rangos numéricos si existen
        if ($question->type === 'number' && ($question->min_value !== null || $question->max_value !== null)) {
            $numValue = (float) $processedValue;
            if ($question->min_value !== null && $numValue < (float) $question->min_value) {
                return true;
            }
            if ($question->max_value !== null && $numValue > (float) $question->max_value) {
                return true;
            }
        }

        return false;
    }
}
