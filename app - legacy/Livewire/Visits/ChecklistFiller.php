<?php

namespace App\Livewire\Visits;

use App\Models\ChecklistAssignment;
use App\Models\VisitCertificate;
use App\Services\VisitService;
use Livewire\Component;

class ChecklistFiller extends Component
{
    public VisitCertificate $visit;
    public ChecklistAssignment $assignment;
    
    public $assignmentId; // Para montado indirecto si fuera necesario
    public $responses = [];
    public $observations = [];
    public $showModal = false;

    // Cache de preguntas para renderizado
    public $sortedQuestions = [];
    public $sections = [];

    protected $visitService;

    public function boot(VisitService $visitService)
    {
        $this->visitService = $visitService;
    }

    public function mount(VisitCertificate $visit, ChecklistAssignment $assignment)
    {
        $this->visit = $visit;
        $this->assignment = $assignment;
        $this->initializeChecklist();
    }

    public function initializeChecklist()
    {
        // Cargar preguntas y secciones
        $checklist = $this->assignment->checklist()->with(['sections', 'questions'])->first();
        
        if (!$checklist) return;

        // Organizar preguntas
        // 1. Preguntas sin sección (General)
        $generalQuestions = $checklist->questions->whereNull('section_id')->sortBy('order');
        
        // 2. Secciones con sus preguntas
        $this->sections = $checklist->sections->sortBy('order')->map(function($section) use ($checklist) {
            $section->questions = $checklist->questions->where('section_id', $section->id)->sortBy('order');
            return $section;
        });

        // Inicializar respuestas vacías
        foreach ($checklist->questions as $question) {
            // Si es checkbox, inicializar array, sino string
            $this->responses[$question->id] = $question->type === 'checkbox' ? [] : '';
        }
    }

    public function save()
    {
        // Validar respuestas (básico: required si es requerido)
        $rules = [];
        $messages = [];
        
        $checklist = $this->assignment->checklist;
        foreach ($checklist->questions as $question) {
            if ($question->is_required) {
                $rules["responses.{$question->id}"] = 'required';
                $messages["responses.{$question->id}.required"] = "La pregunta '{$question->text}' es obligatoria.";
            }
        }

        if (!empty($rules)) {
            $this->validate($rules, $messages);
        }

        // Formatear respuestas para incluir metadata (label y observación)
        $formattedResponses = [];
        foreach ($checklist->questions as $question) {
            $formattedResponses[] = [
                'question_id' => $question->id,
                'question_label' => $question->label,
                'value' => $this->responses[$question->id],
                'observation' => $this->observations[$question->id] ?? null
            ];
        }

        // Guardar usando el servicio
        $this->visitService->completeChecklist(
            $this->visit,
            $this->assignment,
            $formattedResponses,
            false // isSelfAssigned (podría pasar param si fuera relevante)
        );

        // Emitir evento de éxito y cerrar
        $this->dispatch('checklistCompleted', assignmentId: $this->assignment->id);
    }

    public function close()
    {
        $this->dispatch('closeChecklistFiller');
    }

    public function render()
    {
        return view('livewire.visits.checklist-filler');
    }
}
