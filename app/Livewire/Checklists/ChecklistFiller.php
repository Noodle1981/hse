<?php

namespace App\Livewire\Checklists;

use App\Models\DynamicChecklist;
use App\Models\DynamicInspection;
use App\Models\DynamicAnswer;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ChecklistFiller extends Component
{
    public DynamicChecklist $checklist;
    public array $answers = [];
    public string $notes = '';

    protected function rules(): array
    {
        $rules = ['notes' => 'nullable|string'];
        
        foreach ($this->checklist->questions as $question) {
            $key = "answers.{$question->id}";
            if ($question->is_required) {
                $rules[$key] = 'required';
            } else {
                $rules[$key] = 'nullable';
            }
        }

        return $rules;
    }

    protected function messages(): array
    {
        $messages = [];
        
        foreach ($this->checklist->questions as $question) {
            $messages["answers.{$question->id}.required"] = 'Este campo es obligatorio.';
        }

        return $messages;
    }

    public function mount(int $id)
    {
        $this->checklist = DynamicChecklist::with('questions')->findOrFail($id);
        
        // Inicializar respuestas vacías
        foreach ($this->checklist->questions as $question) {
            $this->answers[$question->id] = $question->type === 'checkbox' ? [] : '';
        }
    }

    public function save()
    {
        $this->validate();

        // Obtener user_id: autenticado, o el primero disponible, o null
        $userId = Auth::id();
        if (!$userId) {
            $firstUser = \App\Models\User::first();
            $userId = $firstUser?->id;
        }

        // Crear inspección (primero como borrador para poder guardar respuestas y luego evaluar)
        $inspection = DynamicInspection::create([
            'dynamic_checklist_id' => $this->checklist->id,
            'user_id' => $userId,
            'completed_at' => null,
            'notes' => $this->notes ?: null,
            'status' => DynamicInspection::STATUS_DRAFT,
        ]);

        // Guardar respuestas
        foreach ($this->answers as $questionId => $value) {
            DynamicAnswer::create([
                'dynamic_inspection_id' => $inspection->id,
                'dynamic_question_id' => $questionId,
                'value' => is_array($value) ? json_encode($value) : $value,
            ]);
        }

        // Ahora marcar como completada (esto disparará la evaluación de resultados)
        $inspection->markAsCompleted();

        $route = Auth::user()?->rol === 'Administrador' 
            ? 'gestor.checklists.index' 
            : 'tecnico.checklists.tasks.index';
            
        session()->flash('message', '¡Checklist completado con éxito!');
        return redirect()->route($route);
    }

    public function render()
    {
        return view('livewire.checklists.checklist-filler')
            ->layout('components.layouts.app');
    }
}
