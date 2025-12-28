<?php

namespace App\Livewire\Checklists;

use App\Models\DynamicChecklist;
use App\Models\DynamicQuestion;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\ChecklistSection;
use Livewire\Component;

class ChecklistBuilder extends Component
{
    public ?DynamicChecklist $checklist = null;
    
    // Datos del checklist
    public string $title = '';
    public string $description = '';
    public string $category = ''; // Legacy - mantener por compatibilidad
    public ?int $categoryId = null;
    public ?int $subcategoryId = null;
    public string $registryCode = '';
    public bool $is_active = true;

    // Secciones
    public bool $useSections = false;
    public array $sections = [];
    public string $newSectionTitle = '';
    public ?int $currentSectionId = null;

    // Preguntas del checklist
    public array $questions = [];

    // Nueva pregunta en edición
    public array $newQuestion = [
        'label' => '',
        'type' => 'text',
        'options' => [],
        'is_required' => false,
        'help_text' => '',
        'section_id' => null,
    ];

    public string $optionInput = '';

    // Datos para selectores
    public $categories = [];
    public $subcategories = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'categoryId' => 'nullable|exists:categories,id',
        'subcategoryId' => 'nullable|exists:subcategories,id',
        'registryCode' => 'nullable|string|max:50',
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
        $this->loadCategories();
        
        if ($id) {
            $this->checklist = DynamicChecklist::with(['questions', 'sections'])->findOrFail($id);
            $this->title = $this->checklist->title;
            $this->description = $this->checklist->description ?? '';
            $this->category = $this->checklist->category ?? '';
            $this->categoryId = $this->checklist->category_id;
            $this->subcategoryId = $this->checklist->subcategory_id;
            $this->registryCode = $this->checklist->registry_code ?? '';
            $this->is_active = $this->checklist->is_active;
            
            // Cargar secciones si existen
            if ($this->checklist->sections->count() > 0) {
                $this->useSections = true;
                $this->sections = $this->checklist->sections->map(fn($s) => [
                    'id' => $s->id,
                    'title' => $s->title,
                    'description' => $s->description ?? '',
                ])->toArray();
            }
            
            $this->questions = $this->checklist->questions->map(function ($q) {
                return [
                    'id' => $q->id,
                    'label' => $q->label,
                    'type' => $q->type,
                    'options' => $q->options ?? [],
                    'is_required' => $q->is_required,
                    'help_text' => $q->help_text ?? '',
                    'section_id' => $q->section_id,
                ];
            })->toArray();
            
            if ($this->categoryId) {
                $this->loadSubcategories();
            }
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

    public function loadCategories()
    {
        $this->categories = Category::active()->orderBy('order')->get();
    }

    public function updatedCategoryId()
    {
        $this->subcategoryId = null;
        $this->loadSubcategories();
    }

    public function loadSubcategories()
    {
        if ($this->categoryId) {
            $this->subcategories = Subcategory::where('category_id', $this->categoryId)
                ->active()
                ->orderBy('order')
                ->get();
        } else {
            $this->subcategories = [];
        }
    }

    public function addSection()
    {
        if (trim($this->newSectionTitle) === '') {
            return;
        }

        $this->sections[] = [
            'id' => 'temp_' . uniqid(),
            'title' => $this->newSectionTitle,
            'description' => '',
        ];
        
        $this->newSectionTitle = '';
    }

    public function removeSection(int $index)
    {
        // Quitar section_id de preguntas que pertenecían a esta sección
        $sectionId = $this->sections[$index]['id'];
        foreach ($this->questions as &$question) {
            if ($question['section_id'] === $sectionId) {
                $question['section_id'] = null;
            }
        }
        
        unset($this->sections[$index]);
        $this->sections = array_values($this->sections);
    }

    private function resetNewQuestion()
    {
        $this->newQuestion = [
            'label' => '',
            'type' => 'text',
            'options' => [],
            'is_required' => false,
            'help_text' => '',
            'section_id' => null,
        ];
        $this->optionInput = '';
    }

    public function save()
    {
        $this->validate();

        // Mantener categoría legacy para compatibilidad
        $legacyCategory = $this->category;
        if ($this->categoryId && !$legacyCategory) {
            $cat = Category::find($this->categoryId);
            $legacyCategory = $cat ? $cat->code : null;
        }

        $checklistData = [
            'title' => $this->title,
            'description' => $this->description ?: null,
            'category' => $legacyCategory, // Legacy
            'category_id' => $this->categoryId,
            'subcategory_id' => $this->subcategoryId,
            'registry_code' => $this->registryCode ?: null,
            'is_active' => $this->is_active,
            'is_draft' => false,
        ];

        if ($this->checklist) {
            $this->checklist->update($checklistData);
            $this->checklist->questions()->delete();
            $this->checklist->sections()->delete();
            $checklist = $this->checklist;
        } else {
            $checklist = DynamicChecklist::create($checklistData);
        }

        // Guardar secciones si están habilitadas
        $sectionMapping = [];
        if ($this->useSections && count($this->sections) > 0) {
            foreach ($this->sections as $index => $section) {
                $savedSection = ChecklistSection::create([
                    'dynamic_checklist_id' => $checklist->id,
                    'title' => $section['title'],
                    'description' => $section['description'] ?? null,
                    'order' => $index,
                ]);
                
                // Mapear IDs temporales a IDs reales
                $sectionMapping[$section['id']] = $savedSection->id;
            }
        }

        // Guardar preguntas
        foreach ($this->questions as $order => $q) {
            $sectionId = null;
            if (isset($q['section_id']) && $q['section_id']) {
                // Si es ID temporal, usar el mapping
                $sectionId = $sectionMapping[$q['section_id']] ?? $q['section_id'];
            }

            DynamicQuestion::create([
                'dynamic_checklist_id' => $checklist->id,
                'label' => $q['label'],
                'type' => $q['type'],
                'options' => !empty($q['options']) ? $q['options'] : null,
                'order' => $order,
                'is_required' => $q['is_required'] ?? false,
                'help_text' => $q['help_text'] ?? null,
                'section_id' => $sectionId,
            ]);
        }

        session()->flash('message', $this->checklist ? 'Checklist actualizado.' : 'Checklist creado.');
        return redirect()->route('checklists.index');
    }

    public function render()
    {
        return view('livewire.checklists.checklist-builder', [
            'categories' => $this->categories,
            'subcategories' => $this->subcategories,
        ])->layout('components.layouts.app');
    }
}
