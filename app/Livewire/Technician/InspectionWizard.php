<?php

namespace App\Livewire\Technician;

use App\Models\ChecklistAssignment;
use App\Models\DynamicChecklist;
use App\Models\DynamicInspection;
use App\Models\DynamicAnswer;
use App\Models\VisitCertificate;
use App\Services\VisitService;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InspectionWizard extends Component
{
    public DynamicChecklist $checklist;
    public ?ChecklistAssignment $assignment = null;
    public ?DynamicInspection $inspection = null;
    #[Url]
    public ?int $visit_id = null;

    public int $currentStep = 0;
    public array $answers = [];
    public string $notes = '';
    public string $supervisorName = '';
    public ?string $supervisorSignature = null;
    public ?string $technicianSignature = null;

    public $sections = [];
    public $totalSteps = 0;
    public $questionsBySection = []; // Cache de preguntas por sección

    public function mount(int $id, mixed $assignment = null)
    {
        $this->checklist = DynamicChecklist::with(['sections.questions', 'questions' => function($q) {
            $q->whereNull('section_id');
        }])->findOrFail($id);

        if ($assignment instanceof ChecklistAssignment) {
            abort_if($assignment->user_id !== Auth::id(), 403);
            $this->assignment = $assignment;
        } elseif ($assignment) {
            // Validar que la asignación pertenece al usuario autenticado
            $this->assignment = ChecklistAssignment::where('id', $assignment)
                ->where('user_id', Auth::id())
                ->firstOrFail();
        }

        // Leer visit_id desde la URL (#[Url]) o desde la sesión (fallback)
        if (!$this->visit_id && session()->has('wizard_visit_id')) {
            $this->visit_id = session()->pull('wizard_visit_id');
        }

        // Preparar secciones
        $this->sections = $this->checklist->sections->sortBy('order')->values()->toArray();
        
        // Cache de preguntas por sección para evitar queries en validateCurrentStep
        foreach ($this->checklist->sections as $section) {
            $this->questionsBySection[$section->id] = $section->questions;
        }
        
        // Total steps: Intro (0) + Secciones (N) + Firmas (N+1)
        $this->totalSteps = count($this->sections) + 2;

        // Inicializar respuestas con valores por defecto
        foreach ($this->checklist->questions as $question) {
            $this->answers[$question->id] = $question->type === 'checkbox' ? [] : '';
        }

        // Intentar reutilizar un borrador existente si hay asignación
        $existingDraft = null;
        if ($this->assignment) {
            $existingDraft = DynamicInspection::where('checklist_assignment_id', $this->assignment->id)
                ->where('status', 'draft')
                ->latest('id')
                ->first();
        }

        if ($existingDraft) {
            // Retomar el borrador existente y cargar respuestas previas
            $this->inspection = $existingDraft;
            $existingDraft->load('answers');
            foreach ($existingDraft->answers as $answer) {
                $qId = $answer->dynamic_question_id;
                $this->answers[$qId] = json_decode($answer->value, true) ?? $answer->value;
            }
        } else {
            // Crear nuevo borrador
            $this->inspection = DynamicInspection::create([
                'dynamic_checklist_id' => $this->checklist->id,
                'user_id' => Auth::id(),
                'sede_id' => $this->assignment ? $this->assignment->sede_id : null,
                'checklist_assignment_id' => $this->assignment ? $this->assignment->id : null,
                'status' => 'draft',
            ]);
        }

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
            $this->dispatch('scroll-to-top');
        }
    }

    public function prevStep()
    {
        if ($this->currentStep > 0) {
            $this->currentStep--;
            $this->dispatch('scroll-to-top');
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
                $this->validate($rules, [
                    'answers.*.required' => 'Este campo es obligatorio.',
                ], ['answers.*' => 'Este campo']);
            }
        }
    }

    public function saveProgress()
    {
        try {
            // Guardar respuestas intermedias
            DB::transaction(function () {
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
            Log::error('Error guardando progreso de inspección', [
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
        ], [
            'supervisorName.required' => 'El nombre del responsable es obligatorio.',
            'supervisorSignature.required' => 'La firma del responsable es obligatoria.',
            'technicianSignature.required' => 'Tu firma es obligatoria.',
        ]);

        $this->saveProgress();

        $this->inspection->update([
            'status' => 'completed',
            'completed_at' => now(),
            'supervisor_name' => $this->supervisorName,
            'supervisor_signature' => $this->supervisorSignature,
            'technician_signature' => $this->technicianSignature,
        ]);

        if ($this->assignment) {
            // Si estamos en el contexto de una visita, registrar la completación via VisitService
            if ($this->visit_id) {
                $visit = VisitCertificate::find($this->visit_id);
                if ($visit) {
                    // Recopilar TODAS las preguntas: las de secciones + las sin sección
                    $allQuestions = collect();
                    foreach ($this->checklist->sections as $section) {
                        $allQuestions = $allQuestions->merge($section->questions);
                    }
                    $allQuestions = $allQuestions->merge($this->checklist->questions);

                    $formattedResponses = [];
                    foreach ($allQuestions as $question) {
                        $formattedResponses[] = [
                            'question_id'    => $question->id,
                            'question_label' => $question->label,
                            'value'          => $this->answers[$question->id] ?? '',
                            'observation'    => null,
                        ];
                    }

                    try {
                        app(VisitService::class)->completeChecklist(
                            $visit,
                            $this->assignment,
                            $formattedResponses
                        );
                    } catch (\Exception $e) {
                        Log::error('VisitService::completeChecklist falló', ['error' => $e->getMessage()]);
                        $this->assignment->update(['status' => 'completed']);
                    }
                }
            } else {
                $this->assignment->update(['status' => 'completed']);
            }
        }

        session()->flash('message', '¡Inspección completada exitosamente!');

        if ($this->visit_id) {
            $user = Auth::user();
            $prefix = ($user instanceof User && $user->isAdmin()) ? 'gestor' : 'tecnico';
            return redirect()->route($prefix . '.visits.in-progress', $this->visit_id)
                ->with('message', '¡Checklist completado! Puedes continuar con la visita.');
        }

        return redirect()->route('tecnico.inspections.show', $this->inspection->id);
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.technician.inspection-wizard');
    }
}
