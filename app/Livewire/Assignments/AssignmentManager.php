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
    public string $statusFilter = 'all';

    // Formulario de nueva asignación
    public bool $showForm = false;
    public ?int $formChecklistId = null;
    public ?int $formUserId = null;
    public ?int $formSedeId = null;
    public string $formFrequency = 'once';
    public ?string $formDueDate = null;
    public string $formNotes = '';

    // Datos para selectores
    public $empresas;
    public $sedes = [];
    public $tecnicos = [];
    public $checklists;

    protected $rules = [
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
        $this->checklists = DynamicChecklist::where('is_active', true)->orderBy('title')->get();
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

    public function updatedStatusFilter()
    {
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

    public function updatedFormSedeId()
    {
        $this->formUserId = null;
        $this->loadTecnicos();
    }

    public function save()
    {
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
        ChecklistAssignment::findOrFail($id)->delete();
        session()->flash('message', 'Asignación eliminada');
        $this->resetPage();
    }

    private function resetForm()
    {
        $this->formChecklistId = null;
        $this->formUserId = null;
        $this->formSedeId = null;
        $this->formFrequency = 'once';
        $this->formDueDate = null;
        $this->formNotes = '';
        $this->tecnicos = [];
    }

    public function render()
    {
        $query = ChecklistAssignment::with(['checklist', 'user', 'sede', 'empresa'])
            ->orderBy('created_at', 'desc');

        // Aplicar filtros
        if ($this->selectedEmpresa) {
            $query->where('empresa_id', $this->selectedEmpresa);
        }

        if ($this->selectedSede) {
            $query->where('sede_id', $this->selectedSede);
        }

        if ($this->statusFilter !== 'all') {
            if ($this->statusFilter === 'overdue') {
                $query->overdue();
            } else {
                $query->where('status', $this->statusFilter);
            }
        }

        $assignments = $query->paginate(15);

        return view('livewire.assignments.assignment-manager', [
            'assignments' => $assignments,
        ])->layout('components.layouts.app');
    }
}
