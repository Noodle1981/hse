<?php

namespace App\Livewire\Checklists;

use App\Models\DynamicChecklist;
use App\Models\DynamicQuestion;
use Livewire\Component;

class ChecklistBuilder extends Component
{
    public ?DynamicChecklist $checklist = null;
    
    // Datos del checklist
    public string $title = '';
    public string $description = '';
    public string $category = '';
    public bool $is_active = true;

    // Preguntas del checklist
    public array $questions = [];

    // Nueva pregunta en edición
    public array $newQuestion = [
        'label' => '',
        'type' => 'text',
        'options' => [],
        'is_required' => false,
        'help_text' => '',
    ];

    public string $optionInput = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'category' => 'nullable|string|max:50',
        'questions' => 'required|array|min:1',
        'questions.*.label' => 'required|string|max:500',
        'questions.*.type' => 'required|in:text,textarea,select,radio,checkbox,number,date,signature',
    ];

    protected $messages = [
        'questions.required' => 'Debe agregar al menos una pregunta.',
        'questions.min' => 'Debe agregar al menos una pregunta.',
    ];

    public function mount(?int $id = null)
    {
        if ($id) {
            $this->checklist = DynamicChecklist::with('questions')->findOrFail($id);
            $this->title = $this->checklist->title;
            $this->description = $this->checklist->description ?? '';
            $this->category = $this->checklist->category ?? '';
            $this->is_active = $this->checklist->is_active;
            $this->questions = $this->checklist->questions->map(function ($q) {
                return [
                    'id' => $q->id,
                    'label' => $q->label,
                    'type' => $q->type,
                    'options' => $q->options ?? [],
                    'is_required' => $q->is_required,
                    'help_text' => $q->help_text ?? '',
                ];
            })->toArray();
        }
    }

    public function addOption()
    {
        if (trim($this->optionInput) !== '') {
            $this->newQuestion['options'][] = trim($this->optionInput);
            $this->optionInput = '';
        }
    }

    public function removeOption(int $index)
    {
        unset($this->newQuestion['options'][$index]);
        $this->newQuestion['options'] = array_values($this->newQuestion['options']);
    }

    public function addQuestion()
    {
        if (trim($this->newQuestion['label']) === '') {
            $this->addError('newQuestion.label', 'La pregunta es requerida.');
            return;
        }

        $this->questions[] = $this->newQuestion;
        $this->resetNewQuestion();
    }

    public function removeQuestion(int $index)
    {
        unset($this->questions[$index]);
        $this->questions = array_values($this->questions);
    }

    public function moveUp(int $index)
    {
        if ($index > 0) {
            $temp = $this->questions[$index - 1];
            $this->questions[$index - 1] = $this->questions[$index];
            $this->questions[$index] = $temp;
        }
    }

    public function moveDown(int $index)
    {
        if ($index < count($this->questions) - 1) {
            $temp = $this->questions[$index + 1];
            $this->questions[$index + 1] = $this->questions[$index];
            $this->questions[$index] = $temp;
        }
    }

    private function resetNewQuestion()
    {
        $this->newQuestion = [
            'label' => '',
            'type' => 'text',
            'options' => [],
            'is_required' => false,
            'help_text' => '',
        ];
        $this->optionInput = '';
    }

    public function save()
    {
        $this->validate();

        $checklistData = [
            'title' => $this->title,
            'description' => $this->description ?: null,
            'category' => $this->category ?: null,
            'is_active' => $this->is_active,
            'is_draft' => false, // Al guardar, ya no es borrador
        ];

        if ($this->checklist) {
            $this->checklist->update($checklistData);
            $this->checklist->questions()->delete();
            $checklist = $this->checklist;
        } else {
            $checklist = DynamicChecklist::create($checklistData);
        }

        foreach ($this->questions as $order => $q) {
            DynamicQuestion::create([
                'dynamic_checklist_id' => $checklist->id,
                'label' => $q['label'],
                'type' => $q['type'],
                'options' => !empty($q['options']) ? $q['options'] : null,
                'order' => $order,
                'is_required' => $q['is_required'] ?? false,
                'help_text' => $q['help_text'] ?? null,
            ]);
        }

        session()->flash('message', $this->checklist ? 'Checklist actualizado.' : 'Checklist creado.');
        return redirect()->route('checklists.index');
    }

    public function render()
    {
        return view('livewire.checklists.checklist-builder')
            ->layout('components.layouts.app');
    }
}
