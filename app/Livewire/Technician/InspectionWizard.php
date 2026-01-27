<?php

namespace App\Livewire\Technician;

use App\Models\ChecklistAssignment;
use App\Models\DynamicChecklist;
use App\Models\DynamicInspection;
use App\Models\DynamicAnswer;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class InspectionWizard extends Component
{
    public DynamicChecklist $checklist;
    public ?ChecklistAssignment $assignment = null;
    public ?DynamicInspection $inspection = null;

    public int $currentStep = 0;
    public array $answers = [];
    public string $notes = '';
    public string $supervisorName = '';
    public ?string $supervisorSignature = null;
    public ?string $technicianSignature = null;

    public $sections = [];
    public $totalSteps = 0;
    public $questionsBySection = []; // Cache de preguntas por sección

    public function mount(int $id, ?int $assignment = null)
    {
        $this->checklist = DynamicChecklist::with(['sections.questions', 'questions' => function($q) {
            $q->whereNull('section_id');
        }])->findOrFail($id);

        if ($assignment) {
            // Validar que la asignación pertenece al usuario autenticado
            $this->assignment = ChecklistAssignment::where('id', $assignment)
                ->where('user_id', auth()->id())
                ->firstOrFail();
        }

        // Preparar secciones
        $this->sections = $this->checklist->sections->sortBy('order')->values()->toArray();
        
        // Cache de preguntas por sección para evitar queries en validateCurrentStep
        foreach ($this->checklist->sections as $section) {
            $this->questionsBySection[$section->id] = $section->questions;
        }
        
        // Total steps: Intro (0) + Secciones (N) + Firmas (N+1)
        $this->totalSteps = count($this->sections) + 2;

        // Inicializar respuestas
        foreach ($this->checklist->questions as $question) {
            $this->answers[$question->id] = $question->type === 'checkbox' ? [] : '';
        }

        // Crear borrador inicial de inspección
        $this->inspection = DynamicInspection::create([
            'dynamic_checklist_id' => $this->checklist->id,
            'user_id' => Auth::id(),
            'sede_id' => $this->assignment ? $this->assignment->sede_id : null,
            'checklist_assignment_id' => $this->assignment ? $this->assignment->id : null,
            'status' => 'draft',
        ]);

        if ($this->assignment) {
            $this->assignment->update(['status' => 'in_progress']);
        }
    }

    public function nextStep()
    {
        if ($this->currentStep < $this->totalSteps - 1) {
            $this->validateCurrentStep();
            $this->currentStep++;
            $this->saveProgress();
        }
    }

    public function prevStep()
    {
        if ($this->currentStep > 0) {
            $this->currentStep--;
        }
    }

    protected function validateCurrentStep()
    {
        // Solo validar si es un paso de sección
        if ($this->currentStep > 0 && $this->currentStep <= count($this->sections)) {
            $section = $this->sections[$this->currentStep - 1];
            
            // Usar cache en lugar de query
            $questions = $this->questionsBySection[$section['id']] ?? collect();
            
            $rules = [];
            foreach ($questions as $question) {
                if ($question->is_required) {
                    $rules["answers.{$question->id}"] = 'required';
                }
            }
            
            if (!empty($rules)) {
                $this->validate($rules, [], ['answers.*' => 'Este campo']);
            }
        }
    }

    public function saveProgress()
    {
        try {
            // Guardar respuestas intermedias
            \DB::transaction(function () {
                foreach ($this->answers as $questionId => $value) {
                    DynamicAnswer::updateOrCreate(
                        [
                            'dynamic_inspection_id' => $this->inspection->id,
                            'dynamic_question_id' => $questionId
                        ],
                        ['value' => is_array($value) ? json_encode($value) : $value]
                    );
                }

                $this->inspection->update(['notes' => $this->notes]);
            });
        } catch (\Exception $e) {
            \Log::error('Error guardando progreso de inspección', [
                'inspection_id' => $this->inspection->id,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Error al guardar el progreso. Por favor, intenta nuevamente.');
        }
    }

    public function complete()
    {
        // Validar firmas y notas en el último paso
        $this->validate([
            'supervisorName' => 'required|string|max:255',
            'supervisorSignature' => 'required|string',
            'technicianSignature' => 'required|string',
        ]);

        $this->saveProgress();

        $this->inspection->update([
            'status' => $this->inspection->sede_id ? 'completed' : 'draft',
            'completed_at' => now(),
            'supervisor_name' => $this->supervisorName,
            'supervisor_signature' => $this->supervisorSignature,
            'technician_signature' => $this->technicianSignature,
        ]);

        if ($this->assignment) {
            $this->assignment->update(['status' => 'completed']);
        }

        session()->flash('message', '¡Inspección completada exitosamente!');
        return redirect()->route('tecnico.inspections.show', $this->inspection->id);
    }

    public function render()
    {
        return view('livewire.technician.inspection-wizard')
            ->layout('components.layouts.app');
    }
}
