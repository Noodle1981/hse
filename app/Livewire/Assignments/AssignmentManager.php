<?php

namespace App\Livewire\Assignments;

use App\Models\ChecklistAssignment;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\User;
use App\Models\DynamicChecklist;
use Livewire\Component;
use Livewire\WithPagination;

class AssignmentManager extends Component
{
    use WithPagination;

    // Filtros
    public ?int $selectedEmpresa = null;
    public ?int $selectedSede = null;

    // Formulario de nueva asignación
    public bool $showForm = false;
    public ?int $editingId = null;
    public ?int $formEmpresaId = null;
    public ?int $formCategoryId = null;
    public ?int $formSubcategoryId = null;
    public ?int $formChecklistId = null;
    public ?int $formUserId = null;
    public ?int $formSedeId = null;
    public string $formFrequency = 'once';
    public ?string $formDueDate = null;
    public string $formNotes = '';

    // Datos para selectores
    public $empresas;
    public $categories = [];
    public $subcategories = [];
    public $sedes = [];
    public $tecnicos = [];
    public $checklists = [];

    protected $rules = [
        'formEmpresaId' => 'required|exists:empresas,id',
        'formChecklistId' => 'required|exists:dynamic_checklists,id',
        'formUserId' => 'required|exists:users,id',
        'formSedeId' => 'required|exists:sedes,id',
        'formFrequency' => 'required|in:once,weekly,monthly,quarterly,yearly',
        'formDueDate' => 'nullable|date|after:today',
        'formNotes' => 'nullable|string|max:500',
    ];

    public function mount()
    {
        $this->empresas = Empresa::orderBy('nombre')->get();
        $this->categories = \App\Models\Category::where('is_active', true)->orderBy('order')->get();
    }

    public function updatedSelectedEmpresa()
    {
        $this->selectedSede = null;
        $this->loadSedes();
        $this->resetPage();
    }

    public function updatedSelectedSede()
    {
        $this->loadTecnicos();
        $this->resetPage();
    }

    public function loadSedes()
    {
        if ($this->selectedEmpresa) {
            $this->sedes = Sede::where('empresa_id', $this->selectedEmpresa)
                ->orderBy('nombre_sede')
                ->get();
        } else {
            $this->sedes = [];
        }
    }

    public function loadTecnicos()
    {
        if ($this->selectedSede) {
            $sede = Sede::find($this->selectedSede);
            $this->tecnicos = $sede->users()->orderBy('apellidos')->get();
        } else {
            $this->tecnicos = [];
        }
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetForm();
        } else {
            // Cargar checklists inicialmente (todos los activos)
            $this->loadChecklists();
            
            // Pre-seleccionar sede si hay una en los filtros
            if ($this->selectedSede) {
                $this->formSedeId = $this->selectedSede;
                $this->loadTecnicos();
            } elseif ($this->selectedEmpresa) {
                // Si solo hay empresa seleccionada, cargar sus sedes
                $this->loadSedes();
            }
        }
    }

    public function updatedFormEmpresaId()
    {
        $this->formSedeId = null;
        $this->formUserId = null;
        $this->tecnicos = [];
    }

    public function updatedFormCategoryId()
    {
        $this->formSubcategoryId = null;
        $this->formChecklistId = null;
        $this->loadSubcategories();
        $this->loadChecklists();
    }

    public function updatedFormSubcategoryId()
    {
        $this->formChecklistId = null;
        $this->loadChecklists();
    }

    public function loadSubcategories()
    {
        if ($this->formCategoryId) {
            $this->subcategories = \App\Models\Subcategory::where('category_id', $this->formCategoryId)
                ->where('is_active', true)
                ->orderBy('order')
                ->get();
        } else {
            $this->subcategories = [];
        }
    }

    public function loadChecklists()
    {
        $query = DynamicChecklist::where('is_active', true);
        
        if ($this->formCategoryId) {
            $query->where('category_id', $this->formCategoryId);
        }
        
        if ($this->formSubcategoryId) {
            $query->where('subcategory_id', $this->formSubcategoryId);
        }
        
        $this->checklists = $query->orderBy('title')->get();
    }

    public function updatedFormSedeId()
    {
        $this->formUserId = null;
        if ($this->formSedeId) {
            $sede = Sede::find($this->formSedeId);
            $this->tecnicos = $sede->users()->orderBy('apellidos')->get();
        } else {
            $this->tecnicos = [];
        }
    }

    public function edit(int $id)
    {
        $assignment = ChecklistAssignment::with(['checklist', 'sede.empresa'])->findOrFail($id);
        
        // Verificar que se puede editar
        if (!$assignment->canBeEdited()) {
            session()->flash('error', 'Esta asignación no puede ser editada.');
            return;
        }

        $this->editingId = $assignment->id;
        $this->formEmpresaId = $assignment->empresa_id;
        $this->formSedeId = $assignment->sede_id;
        $this->formChecklistId = $assignment->dynamic_checklist_id;
        $this->formUserId = $assignment->user_id;
        $this->formDueDate = $assignment->due_date?->format('Y-m-d');
        $this->formNotes = $assignment->notes ?? '';
        $this->formFrequency = $assignment->frequency;
        
        // Cargar técnicos de la sede
        $sede = Sede::find($this->formSedeId);
        $this->tecnicos = $sede->users()->orderBy('apellidos')->get();
        
        $this->showForm = true;
    }

    public function update()
    {
        $this->validate([
            'formUserId' => 'required|exists:users,id',
            'formDueDate' => 'nullable|date|after:today',
            'formNotes' => 'nullable|string|max:500',
        ]);

        $assignment = ChecklistAssignment::findOrFail($this->editingId);
        
        // Verificar que se puede editar
        if (!$assignment->canBeEdited()) {
            session()->flash('error', 'Esta asignación no puede ser editada.');
            return;
        }

        // Verificar que el usuario esté asignado a la sede
        $sede = Sede::findOrFail($assignment->sede_id);
        if (!$sede->users()->where('user_id', $this->formUserId)->exists()) {
            $this->addError('formUserId', 'El técnico seleccionado no está asignado a esta sede.');
            return;
        }

        $assignment->update([
            'user_id' => $this->formUserId,
            'due_date' => $this->formDueDate,
            'notes' => $this->formNotes ?: null,
        ]);

        session()->flash('message', 'Asignación actualizada exitosamente');
        $this->resetForm();
        $this->showForm = false;
        $this->resetPage();
    }

    public function save()
    {
        if ($this->editingId) {
            $this->update();
            return;
        }

        $this->validate();

        // Obtener empresa_id de la sede
        $sede = Sede::findOrFail($this->formSedeId);

        // Verificar que el usuario esté asignado a la sede
        if (!$sede->users()->where('user_id', $this->formUserId)->exists()) {
            $this->addError('formUserId', 'El técnico seleccionado no está asignado a esta sede.');
            return;
        }

        ChecklistAssignment::create([
            'dynamic_checklist_id' => $this->formChecklistId,
            'user_id' => $this->formUserId,
            'sede_id' => $this->formSedeId,
            'empresa_id' => $sede->empresa_id,
            'frequency' => $this->formFrequency,
            'due_date' => $this->formDueDate,
            'status' => 'pending',
            'notes' => $this->formNotes ?: null,
        ]);

        session()->flash('message', 'Asignación creada exitosamente');
        $this->resetForm();
        $this->showForm = false;
        $this->resetPage();
    }

    public function delete(int $id)
    {
        $assignment = ChecklistAssignment::findOrFail($id);
        
        if (!$assignment->canBeDeleted()) {
            session()->flash('error', 'No se puede eliminar una asignación completada.');
            return;
        }

        $assignment->delete();
        session()->flash('message', 'Asignación eliminada');
        $this->resetPage();
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->formEmpresaId = null;
        $this->formCategoryId = null;
        $this->formSubcategoryId = null;
        $this->formChecklistId = null;
        $this->formUserId = null;
        $this->formSedeId = null;
        $this->formFrequency = 'once';
        $this->formDueDate = null;
        $this->formNotes = '';
        $this->subcategories = [];
        $this->checklists = [];
        $this->tecnicos = [];
    }

    public function render()
    {
        $query = ChecklistAssignment::with(['checklist', 'user', 'sede', 'empresa', 'visitCompletion.visit'])
            ->orderBy('created_at', 'desc');

        // Aplicar filtros
        if ($this->selectedEmpresa) {
            $query->where('empresa_id', $this->selectedEmpresa);
        }

        if ($this->selectedSede) {
            $query->where('sede_id', $this->selectedSede);
        }

        $assignments = $query->paginate(15);

        return view('livewire.assignments.assignment-manager', [
            'assignments' => $assignments,
        ])->layout('components.layouts.app');
    }
}
