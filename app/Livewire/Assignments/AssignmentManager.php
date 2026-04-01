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

    #[\Livewire\Attributes\Url]
    public string $activeTab = 'inspections';

    protected $queryString = [
        'selectedEmpresa' => ['except' => null],
        'selectedSede' => ['except' => null],
        'activeTab' => ['except' => 'inspections'],
    ];

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

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
        $month = strtolower(now()->format('F'));

        // 1. Manual Assignments (Solicitudes)
        $manualQuery = ChecklistAssignment::with(['checklist', 'user', 'sede', 'empresa', 'visitCompletion.visit', 'inspection'])
            ->when($this->selectedEmpresa, function ($q) {
                $q->where('empresa_id', $this->selectedEmpresa);
            })
            ->when($this->selectedSede, function ($q) {
                $q->where('sede_id', $this->selectedSede);
            })
            ->orderBy('created_at', 'desc');
        // 1.1 Manual Trainings (Solicitudes de Capacitación)
        $trainingsQuery = \App\Models\Training::with(['sede', 'empresa', 'responsibleUser'])
            ->where('status', '!=', 'completed')
            ->when($this->selectedEmpresa, function ($q) {
                $q->where('empresa_id', $this->selectedEmpresa);
            })
            ->when($this->selectedSede, function ($q) {
                $q->where('sede_id', $this->selectedSede);
            });

        // 1.2 Manual Reports (Informes Maestros en Borrador)
        $reportsQuery = \App\Models\MasterReport::with(['sede', 'sede.empresa', 'tecnico'])
            ->where('estado', '!=', 'finalizado')
            ->when($this->selectedEmpresa, function ($q) {
                $q->whereHas('sede', function($sub) {
                    $sub->where('empresa_id', $this->selectedEmpresa);
                });
            })
            ->when($this->selectedSede, function ($q) {
                $q->where('sede_id', $this->selectedSede);
            });

        $manual = $manualQuery->get()->map(fn($a) => (object)[
            'id' => $a->id,
            'dynamic_checklist_id' => $a->dynamic_checklist_id,
            'checklist' => $a->checklist,
            'user' => $a->user,
            'sede' => $a->sede,
            'empresa' => $a->empresa,
            'due_date' => $a->due_date,
            'status' => $a->status,
            'source' => 'manual',
            'category' => 'Inspección',
            'is_overdue' => method_exists($a, 'isOverdue') ? $a->isOverdue() : false,
            'registry_code' => $a->checklist?->registry_code,
            'created_at' => $a->created_at,
            'inspection' => $a->inspection ?? ($a->completion?->inspection ?? null),
            'frequency_label' => method_exists($a, 'getFrequencyLabel') ? $a->getFrequencyLabel() : 'Manual',
            'can_be_edited' => method_exists($a, 'canBeEdited') ? $a->canBeEdited() : true,
            'can_be_deleted' => method_exists($a, 'canBeDeleted') ? $a->canBeDeleted() : true,
            'status_badge_color' => method_exists($a, 'getStatusBadgeColor') ? $a->getStatusBadgeColor() : 'gray'
        ]);

        $manualTrainings = $trainingsQuery->get()->map(fn($t) => (object)[
            'id' => $t->id,
            'dynamic_checklist_id' => $t->dynamic_checklist_id,
            'checklist' => (object)['title' => $t->title, 'registry_code' => 'CAP-' . $t->id],
            'user' => $t->responsibleUser,
            'sede' => $t->sede,
            'empresa' => $t->empresa,
            'due_date' => $t->date,
            'status' => $t->status,
            'source' => 'manual',
            'category' => 'Capacitación',
            'is_overdue' => $t->date?->isPast() && $t->status !== 'completed',
            'registry_code' => 'CAP-' . $t->id,
            'created_at' => $t->created_at,
            'inspection' => null,
            'frequency_label' => 'Manual',
            'can_be_edited' => false,
            'can_be_deleted' => true,
            'status_badge_color' => 'blue',
            'duration' => $t->duration,
            'type' => $t->type
        ]);

        $manualReports = $reportsQuery->get()->map(fn($r) => (object)[
            'id' => $r->id,
            'dynamic_checklist_id' => null,
            'checklist' => (object)['title' => $r->titulo, 'registry_code' => 'REP-' . $r->id],
            'user' => $r->tecnico,
            'sede' => $r->sede,
            'empresa' => $r->sede?->empresa,
            'due_date' => $r->created_at->addDays(7),
            'status' => $r->estado,
            'source' => 'manual',
            'category' => 'Informe',
            'is_overdue' => false,
            'registry_code' => 'REP-' . $r->id,
            'created_at' => $r->created_at,
            'inspection' => null,
            'frequency_label' => 'Manual',
            'can_be_edited' => true,
            'can_be_deleted' => true,
            'status_badge_color' => 'indigo',
            'periodo' => $r->periodo
        ]);

        // 2. Annual Plan Tasks (Contratos)
        $contractQuery = \App\Models\AnnualPlan::with(['checklist', 'sede', 'sede.empresa', 'sede.users'])
            ->where($month, true)
            ->where('year', now()->year)
            ->when($this->selectedEmpresa, function ($q) {
                $q->whereHas('sede', function($sub) {
                    $sub->where('empresa_id', $this->selectedEmpresa);
                });
            })
            ->when($this->selectedSede, function ($q) {
                $q->where('sede_id', $this->selectedSede);
            });

        $contract = $contractQuery->get()->map(fn($p) => (object)[
            'id' => null,
            'dynamic_checklist_id' => $p->checklist_id,
            'checklist' => $p->checklist ?? (object)['title' => $p->activity_name, 'registry_code' => $p->activity_code],
            'user' => (object)[
                'nombres' => 'Operadores',
                'apellidos' => 'Sede',
                'id' => null
            ],
            'monitored_users' => $p->sede?->users ?? collect(),
            'sede' => $p->sede,
            'empresa' => $p->sede?->empresa,
            'due_date' => now()->endOfMonth(),
            'status' => 'pending',
            'source' => 'contract',
            'category' => $p->category,
            'is_overdue' => false,
            'registry_code' => $p->checklist?->registry_code ?? $p->activity_code,
            'created_at' => $p->created_at,
            'inspection' => null,
            'frequency_label' => $p->frequency === 'none' ? 'Mensual' : ucfirst($p->frequency),
            'can_be_edited' => false,
            'can_be_deleted' => false,
            'status_badge_color' => 'yellow',
            'duration' => ($p->category === 'Capacitación') ? '60 min' : null,
            'type' => ($p->category === 'Capacitación') ? 'Presencial' : null,
            'periodo' => ($p->category === 'Informe') ? now()->translatedFormat('F Y') : null
        ]);

        // Merge all into categorized collections (con manejo defensivo de nulos)
        $allInspections = $manual->concat($contract->where('category', 'Inspección'))->sortByDesc('created_at');
        $allTrainings = $manualTrainings->concat($contract->where('category', 'Capacitación'))->sortByDesc('created_at');
        $allReports = $manualReports->concat($contract->where('category', 'Informe'))->sortByDesc('created_at');

        // Determinar qué colección paginar
        $currentCollection = match($this->activeTab) {
            'trainings' => $allTrainings,
            'reports' => $allReports,
            default => $allInspections
        };
        
        $currentPage = $this->getPage();
        $perPage = 15;
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentCollection->forPage($currentPage, $perPage),
            $currentCollection->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return view('livewire.assignments.assignment-manager', [
            'assignments' => $paginated,
            'inspectionsCount' => $allInspections->count(),
            'trainingsCount' => $allTrainings->count(),
            'reportsCount' => $allReports->count(),
        ])->layout('components.layouts.app');
    }
}
