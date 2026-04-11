<?php

namespace App\Livewire\Assignments;

use App\Models\ChecklistAssignment;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\User;
use App\Models\DynamicChecklist;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class AssignmentManager extends Component
{
    use WithPagination;

    // Filtros
    public ?int $selectedEmpresa = null;
    public ?int $selectedSede = null;

    public string $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedEmpresa' => ['except' => null],
        'selectedSede' => ['except' => null],
        'statusFilter' => ['except' => ''],
    ];

    public string $statusFilter = '';

    public function updatingSearch() { $this->resetPage(); }

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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            $this->empresas = Empresa::orderBy('nombre')->get();
        } else {
            // Scope empresas to those where the user has at least one assigned sede
            $assignedEmpresaIds = $user->sedes()->pluck('empresa_id')->unique();
            $this->empresas = Empresa::whereIn('id', $assignedEmpresaIds)->orderBy('nombre')->get();
        }

        $this->categories = \App\Models\Category::where('is_active', true)->orderBy('order')->get();
        $this->loadSedes();
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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $query = Sede::query();

        // Security scoping: only show sedes assigned to the user unless Super Admin
        if (!$user->isSuperAdmin()) {
            $assignedSedeIds = $user->sedes()->pluck('sedes.id');
            $query->whereIn('id', $assignedSedeIds);
        }

        if ($this->selectedEmpresa) {
            $query->where('empresa_id', $this->selectedEmpresa);
        }

        $this->sedes = $query->orderBy('nombre_sede')->get();
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

    #[Layout('components.layouts.app')]
    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $assignedSedeIds = $user->isSuperAdmin() ? null : $user->sedes()->pluck('sedes.id');

        // 1. Manual Assignments (Solicitudes)
        $manualQuery = ChecklistAssignment::with(['checklist', 'user', 'sede', 'empresa', 'inspection'])
            ->when($assignedSedeIds, function ($q) use ($assignedSedeIds) {
                $q->whereIn('sede_id', $assignedSedeIds);
            })
            ->when($this->search, function ($q) {
                $q->whereHas('checklist', function ($c) {
                    $c->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('registry_code', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedEmpresa, function ($q) {
                $q->whereHas('sede', function($sub) {
                    $sub->where('empresa_id', $this->selectedEmpresa);
                });
            })
            ->when($this->selectedSede, function ($q) {
                $q->where('sede_id', $this->selectedSede);
            })
            ->when($this->statusFilter, function ($q) {
                $status = match($this->statusFilter) {
                    'solicitada' => 'pending',
                    'en_curso' => 'in_progress',
                    'finalizada' => 'completed',
                    default => $this->statusFilter
                };
                $q->where('status', $status);
            })
            ->orderBy('created_at', 'desc');

        $manual = $manualQuery->get()->map(fn($p) => (object)[
            'id' => $p->id,
            'dynamic_checklist_id' => $p->dynamic_checklist_id,
            'checklist' => $p->checklist,
            'user' => $p->user,
            'sede' => $p->sede,
            'empresa' => $p->empresa,
            'due_date' => $p->due_date,
            'status' => $p->status,
            'source' => 'manual',
            'category' => 'Inspección',
            'is_overdue' => $p->due_date < now() && $p->status !== 'completed',
            'registry_code' => $p->checklist?->registry_code,
            'created_at' => $p->created_at,
            'inspection' => $p->inspection,
            'frequency_label' => 'Manual',
            'can_be_edited' => $p->status === 'pending',
            'can_be_deleted' => $p->status === 'pending',
            'status_badge_color' => match($p->status) {
                'pending' => 'yellow',
                'in_progress' => 'indigo',
                'completed' => 'emerald',
                default => 'gray'
            }
        ]);

        // 2. Annual Plan Tasks (Contratos)
        $contractQuery = \App\Models\AnnualPlan::with(['sede.empresa', 'checklist'])
            ->when($assignedSedeIds, function ($q) use ($assignedSedeIds) {
                $q->whereIn('sede_id', $assignedSedeIds);
            })
            ->where('category', 'Inspección')
            ->when($this->search, function ($q) {
                $q->whereHas('checklist', function ($c) {
                    $c->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('registry_code', 'like', '%' . $this->search . '%');
                });
                $q->orWhere('activity_name', 'like', '%' . $this->search . '%');
            })
            ->when($this->selectedEmpresa, function ($q) {
                $q->whereHas('sede', fn($sub) => $sub->where('empresa_id', $this->selectedEmpresa));
            })
            ->when($this->selectedSede, function ($q) {
                $q->where('sede_id', $this->selectedSede);
            })
            ->when($this->statusFilter, function ($q) {
                // El Annual Plan es virtual, 'pending' es lo usual para 'solicitada' o 'scheduled' para 'programada'
                if ($this->statusFilter === 'programada') { $q->whereRaw('1=1'); } // Siempre se muestran si no son 'completed'
                else { $q->whereRaw('0=1'); } // Otros estados no aplican directamente a AnnualPlan base
            });

        $contract = $contractQuery->get()->map(fn($p) => (object)[
            'id' => null,
            'dynamic_checklist_id' => $p->checklist_id,
            'checklist' => $p->checklist ?? (object)['title' => $p->activity_name, 'registry_code' => $p->activity_code],
            'user' => $p->sede?->users->first() ?? (object)[
                'nombres' => 'Operadores',
                'apellidos' => 'Sede',
                'id' => null
            ],
            'all_users' => $p->sede?->users, 
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
            'periodo' => now()->translatedFormat('F Y')
        ]);

        // Merge all
        $allInspections = $manual->concat($contract)->sortByDesc('created_at');
        
        $currentPage = $this->getPage();
        $perPage = 15;
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $allInspections->forPage($currentPage, $perPage),
            $allInspections->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return view('livewire.assignments.assignment-manager', [
            'assignments' => $paginated,
        ]);
    }
}
