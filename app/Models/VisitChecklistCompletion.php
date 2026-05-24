<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitChecklistCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_certificate_id',
        'checklist_assignment_id',
        'is_self_assigned',
        'completed_at',
        'responses',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'is_self_assigned' => 'boolean',
        'responses' => 'array',
    ];

    protected $appends = ['failure_analysis', 'formatted_responses'];

    public function visit()
    {
        return $this->belongsTo(VisitCertificate::class, 'visit_certificate_id');
    }

    public function assignment()
    {
        return $this->belongsTo(ChecklistAssignment::class, 'checklist_assignment_id');
    }

    // Relación opcional con Hallazgos que surgieron de este checklist
    public function hallazgos()
    {
        return $this->hasMany(Hallazgo::class, 'checklist_completion_id');
    }

    /**
     * Formatea las respuestas para ser mostradas en reportes (PDF/WEB)
     * Incluye el texto de la pregunta (label) para cada respuesta
     */
    public function getFormattedResponsesAttribute()
    {
        $responses = $this->responses ?? [];
        if (empty($responses)) return [];

        $checklist = $this->assignment?->checklist ?? null;
        if (!$checklist) {
            return array_map(function($resp) {
                return [
                    'question_id' => $resp['question_id'] ?? null,
                    'question_label' => 'Punto de Control',
                    'value' => $resp['value'] ?? 'N/A',
                    'observation' => $resp['observation'] ?? null
                ];
            }, $responses);
        }

        $questions = $checklist->questions->keyBy('id');

        $formatted = [];
        foreach ($responses as $resp) {
            $qId = $resp['question_id'] ?? null;
            $question = $questions->get($qId);
            
            $formatted[] = [
                'question_id' => $qId,
                'question_label' => $question ? $question->label : 'Punto de Control Eliminado',
                'value' => $resp['value'] ?? 'N/A',
                'observation' => $resp['observation'] ?? null
            ];
        }

        return $formatted;
    }

    /**
     * Analiza las respuestas para determinar cuántas fallas existen
     */
    public function getFailureAnalysisAttribute()
    {
        // 1. Si hay una inspección (DynamicInspection) vinculada a través del assignment,
        // usamos sus respuestas reales como única fuente de verdad.
        $assignment = $this->assignment;
        if ($assignment) {
            $inspection = $assignment->inspection;
            if ($inspection && $inspection->status !== 'completed') {
                $completed = \App\Models\DynamicInspection::where('checklist_assignment_id', $assignment->id)
                    ->where('status', 'completed')
                    ->latest('id')
                    ->first();
                if ($completed) {
                    $inspection = $completed;
                }
            }
            if ($inspection) {
                $inspection->loadMissing('answers.question');
                $failedCount = 0;
                $totalCount = $inspection->answers->count();
                foreach ($inspection->answers as $answer) {
                    if ($answer->isFailure()) {
                        $failedCount++;
                    }
                }
                return [
                    'total' => $totalCount,
                    'failed' => $failedCount,
                    'is_failed' => $failedCount > 0
                ];
            }
        }

        // 2. Lógica fallback sobre el campo responses de la completación (ej. migraciones legacy)
        $responses = $this->responses ?? [];
        $checklist = $this->assignment?->checklist ?? null;
        
        if (!$checklist) {
            return [
                'total' => count($responses),
                'failed' => 0,
                'is_failed' => false
            ];
        }

        $questions = $checklist->questions->keyBy('id');
        $failedCount = 0;
        $totalCount = count($responses);

        foreach ($responses as $response) {
            $qId = $response['question_id'] ?? null;
            if (!$qId) continue;

            $question = $questions->get($qId);
            if (!$question) continue;

            $value = $response['value'] ?? null;
            
            if ($this->checkIfValueIsFailure($question, $value)) {
                $failedCount++;
            }
        }

        return [
            'total' => $totalCount,
            'failed' => $failedCount,
            'is_failed' => $failedCount > 0
        ];
    }

    /**
     * Lógica interna para determinar si un valor es falla (reutilizada de DynamicAnswer)
     */
    protected function checkIfValueIsFailure($question, $value): bool
    {
        $processedValue = is_string($value) ? (json_decode($value, true) ?? $value) : $value;

        // 1. Disparadores de falla (failure_values)
        if (!empty($question->failure_values)) {
            $logic = $question->failure_logic ?? DynamicQuestion::LOGIC_FAIL_IF_SELECTED;

            if ($logic === DynamicQuestion::LOGIC_FAIL_IF_MISSING) {
                if (is_array($processedValue)) {
                    foreach ($question->failure_values as $required) {
                        if (!in_array($required, $processedValue)) return true;
                    }
                } else {
                    if (!in_array($processedValue, $question->failure_values)) return true;
                }
            } else {
                if (is_array($processedValue)) {
                    foreach ($processedValue as $val) {
                        if (in_array($val, $question->failure_values)) return true;
                    }
                } else {
                    if (in_array($processedValue, $question->failure_values)) return true;
                }
            }
        }

        // 2. Rangos numéricos
        if ($question->type === 'number' && ($question->min_value !== null || $question->max_value !== null)) {
            $numValue = (float) $processedValue;
            if ($question->min_value !== null && $numValue < (float) $question->min_value) return true;
            if ($question->max_value !== null && $numValue > (float) $question->max_value) return true;
        }

        return false;
    }
}
