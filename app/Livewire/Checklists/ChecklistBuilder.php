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
        'min_value' => null,
        'max_value' => null,
        'failure_values' => [],
        'is_required' => false,
        'help_text' => '',
        'section_id' => null,
        'risk_id' => null,
    ];

    public string $optionInput = '';
    public ?int $editingQuestionIndex = null;

    // Datos para selectores
    public $categories = [];
    public $subcategories = [];

    // Modal de subcategoría
    public bool $showSubcategoryModal = false;
    public array $newSubcategory = [
        'category_id' => null,
        'code' => '',
        'name' => '',
        'description' => '',
    ];



    protected function rules()
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $query = DynamicChecklist::where('title', $value)
                        ->where('category_id', $this->categoryId)
                        ->where('subcategory_id', $this->subcategoryId);
                    
                    // Si estamos editando, excluir el checklist actual
                    if ($this->checklist) {
                        $query->where('id', '!=', $this->checklist->id);
                    }
                    
                    if ($query->exists()) {
                        $fail('Ya existe un checklist con este título, categoría y subcategoría.');
                    }
                },
            ],
            'description' => 'nullable|string',
            'categoryId' => 'nullable|exists:categories,id',
            'subcategoryId' => 'nullable|exists:subcategories,id',
            'registryCode' => 'nullable|string|max:50',
            'questions' => 'required|array|min:1',
            'questions.*.label' => 'required|string|max:500',
            'questions.*.type' => 'required|in:text,textarea,select,radio,checkbox,number,date,signature',
        ];
    }

    protected $messages = [
        'questions.required' => 'Debe agregar al menos una pregunta.',
        'questions.min' => 'Debe agregar al menos una pregunta.',
    ];

    public function mount(?int $id = null)
    {
        $this->loadCategories();
        $this->loadRisks();
        
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
                    'min_value' => $q->min_value,
                    'max_value' => $q->max_value,
                    'failure_values' => $q->failure_values ?? [],
                    'is_required' => $q->is_required,
                    'help_text' => $q->help_text ?? '',
                    'section_id' => $q->section_id,
                    'risk_id' => $q->risk_id,
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
        $option = $this->newQuestion['options'][$index];
        
        // Remove from failure_values if it exists
        if (in_array($option, $this->newQuestion['failure_values'])) {
            $this->newQuestion['failure_values'] = array_values(array_filter(
                $this->newQuestion['failure_values'],
                fn($v) => $v !== $option
            ));
        }

        unset($this->newQuestion['options'][$index]);
        $this->newQuestion['options'] = array_values($this->newQuestion['options']);
    }

    public function toggleFailureValue(string $option)
    {
        if (in_array($option, $this->newQuestion['failure_values'])) {
            $this->newQuestion['failure_values'] = array_values(array_filter(
                $this->newQuestion['failure_values'],
                fn($v) => $v !== $option
            ));
        } else {
            $this->newQuestion['failure_values'][] = $option;
        }
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

    public function editQuestion(int $index)
    {
        $this->editingQuestionIndex = $index;
        // Hacer una copia profunda del array
        $question = $this->questions[$index];
        $this->newQuestion = [
            'label' => $question['label'] ?? '',
            'type' => $question['type'] ?? 'text',
            'options' => $question['options'] ?? [],
            'min_value' => $question['min_value'] ?? null,
            'max_value' => $question['max_value'] ?? null,
            'failure_values' => $question['failure_values'] ?? [],
            'is_required' => $question['is_required'] ?? false,
            'help_text' => $question['help_text'] ?? '',
            'section_id' => $question['section_id'] ?? null,
            'risk_id' => $question['risk_id'] ?? null,
        ];
    }

    public function updateQuestion()
    {
        if (trim($this->newQuestion['label']) === '') {
            $this->addError('newQuestion.label', 'La pregunta es requerida.');
            return;
        }

        $this->questions[$this->editingQuestionIndex] = $this->newQuestion;
        $this->resetNewQuestion();
        $this->editingQuestionIndex = null;
    }

    public function cancelEdit()
    {
        $this->resetNewQuestion();
        $this->editingQuestionIndex = null;
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

    public function loadRisks()
    {
        // For simplicity, we load all risks. 
        // In a real scenario, we might filter by company/sede if the checklist belongs to one.
        $this->availableRisks = \App\Models\Risk::with('matrix')->get();
    }

    public $availableRisks = [];



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

    public function openSubcategoryModal()
    {
        $this->showSubcategoryModal = true;
        // Pre-seleccionar la categoría actual si existe
        if ($this->categoryId) {
            $this->newSubcategory['category_id'] = $this->categoryId;
        }
    }

    public function closeSubcategoryModal()
    {
        $this->showSubcategoryModal = false;
        $this->newSubcategory = [
            'category_id' => null,
            'code' => '',
            'name' => '',
            'description' => '',
        ];
        $this->resetErrorBag();
    }

    public function saveSubcategory()
    {
        $this->validate([
            'newSubcategory.category_id' => 'required|exists:categories,id',
            'newSubcategory.code' => 'required|string|max:50|unique:subcategories,code',
            'newSubcategory.name' => 'required|string|max:255',
            'newSubcategory.description' => 'nullable|string',
        ], [
            'newSubcategory.category_id.required' => 'Debe seleccionar una categoría.',
            'newSubcategory.code.required' => 'El código es requerido.',
            'newSubcategory.code.unique' => 'Ya existe una subcategoría con este código.',
            'newSubcategory.name.required' => 'El nombre es requerido.',
        ]);

        // Obtener el último order de subcategorías de esta categoría
        $lastOrder = Subcategory::where('category_id', $this->newSubcategory['category_id'])
            ->max('order') ?? 0;

        // Crear la subcategoría
        $subcategory = Subcategory::create([
            'category_id' => $this->newSubcategory['category_id'],
            'code' => $this->newSubcategory['code'],
            'name' => $this->newSubcategory['name'],
            'description' => $this->newSubcategory['description'] ?: null,
            'is_active' => true,
            'order' => $lastOrder + 1,
        ]);

        // Actualizar la categoría seleccionada si es diferente
        if ($this->categoryId != $this->newSubcategory['category_id']) {
            $this->categoryId = $this->newSubcategory['category_id'];
        }

        // Recargar subcategorías
        $this->loadSubcategories();

        // Seleccionar automáticamente la nueva subcategoría
        $this->subcategoryId = $subcategory->id;

        // Cerrar modal y mostrar mensaje
        $this->closeSubcategoryModal();
        session()->flash('message', 'Subcategoría creada exitosamente.');
    }

    private function resetNewQuestion()
    {
        $this->newQuestion = [
            'label' => '',
            'type' => 'text',
            'options' => [],
            'min_value' => null,
            'max_value' => null,
            'failure_values' => [],
            'is_required' => false,
            'help_text' => '',
            'section_id' => null,
            'risk_id' => null,
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
                'min_value' => $q['min_value'] !== '' ? $q['min_value'] : null,
                'max_value' => $q['max_value'] !== '' ? $q['max_value'] : null,
                'failure_values' => !empty($q['failure_values']) ? $q['failure_values'] : null,
                'order' => $order,
                'is_required' => $q['is_required'] ?? false,
                'help_text' => $q['help_text'] ?? null,
                'section_id' => $sectionId,
                'risk_id' => $q['risk_id'] ?? null,
            ]);
        }

        session()->flash('message', $this->checklist ? 'Checklist actualizado.' : 'Checklist creado.');
        return redirect()->route('gestor.checklists.index');
    }

    public function render()
    {
        return view('livewire.checklists.checklist-builder', [
            'categories' => $this->categories,
            'subcategories' => $this->subcategories,
            'availableRisks' => $this->availableRisks,
        ])->layout('components.layouts.app');
    }
}
