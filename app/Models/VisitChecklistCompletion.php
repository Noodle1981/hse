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
     * Helper para obtener respuestas siempre en formato array de objetos {question_label, value, observation}
     */
    public function getFormattedResponsesAttribute()
    {
        $responses = $this->responses ?? [];
        $formatted = [];

        // Si ya viene en el formato nuevo (lista de arrays con question_label), lo devolvemos tal cual
        if (isset($responses[0]) && is_array($responses[0]) && isset($responses[0]['question_label'])) {
            return $responses;
        }

        // Si es formato viejo [id => valor], intentamos recuperar etiquetas
        // Cargamos el checklist para obtener las etiquetas de las preguntas
        $checklist = $this->assignment->checklist ?? null;
        $questions = $checklist ? $checklist->questions->keyBy('id') : collect();

        foreach ($responses as $qId => $value) {
            $question = $questions->get($qId);
            $formatted[] = [
                'question_id' => $qId,
                'question_label' => $question ? $question->label : "Pregunta #{$qId}",
                'value' => $value,
                'observation' => null
            ];
        }

        return $formatted;
    }
}
