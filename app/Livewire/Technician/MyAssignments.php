<?php

namespace App\Livewire\Technician;

use App\Models\ChecklistAssignment;
use App\Models\Empresa;
use App\Models\Sede;
use Livewire\Component;
use Livewire\WithPagination;

class MyAssignments extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $selectedEmpresa = null;
    public ?int $selectedSede = null;
    public string $statusFilter = '';

    #[\Livewire\Attributes\Url]
    public string $activeTab = 'inspections';

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedEmpresa' => ['except' => null],
        'selectedSede' => ['except' => null],
        'statusFilter' => ['except' => ''],
        'activeTab' => ['except' => 'inspections'],
    ];

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedEmpresa()
    {
        $this->selectedSede = null;
        $this->resetPage();
    }

    public function updatedSelectedSede()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        $month = strtolower(now()->format('F'));

        // 1. Manual Assignments (Solicitudes)
        $manualQuery = ChecklistAssignment::with(['checklist', 'sede', 'empresa', 'visitCompletion.visit'])
            ->where('user_id', $user->id)
            ->when($this->search, function ($q) {
                $q->whereHas('checklist', function ($sub) {
                    $sub->where('title', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedEmpresa, function ($q) {
                $q->where('empresa_id', $this->selectedEmpresa);
            })
            ->when($this->selectedSede, function ($q) {
                $q->where('sede_id', $this->selectedSede);
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            });

        // 1.1 Manual Trainings (Solicitudes de Capacitación)
        $trainingsQuery = \App\Models\Training::with(['sede', 'empresa'])
            ->where('user_id', $user->id)
            ->where('status', '!=', 'completed')
            ->when($this->search, function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->selectedEmpresa, function ($q) {
                $q->where('empresa_id', $this->selectedEmpresa);
            })
            ->when($this->selectedSede, function ($q) {
                $q->where('sede_id', $this->selectedSede);
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            });

        // 1.2 Manual Reports (Informes Maestros en Borrador)
        $reportsQuery = \App\Models\MasterReport::with(['sede', 'sede.empresa'])
            ->where('user_id', $user->id)
            ->where('estado', '!=', 'finalizado')
            ->when($this->search, function ($q) {
                $q->where('titulo', 'like', '%' . $this->search . '%');
            })
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
            'frequency_label' => method_exists($a, 'getFrequencyLabel') ? $a->getFrequencyLabel() : 'Manual'
        ]);
        $manualReports = $reportsQuery->get()->map(fn($r) => (object)[
            'id' => $r->id,
            'dynamic_checklist_id' => null,
            'checklist' => (object)['title' => $r->titulo, 'registry_code' => 'REP-' . $r->id],
            'sede' => $r->sede,
            'empresa' => $r->sede?->empresa,
            'due_date' => $r->created_at->addDays(7), // Estimación
            'status' => $r->estado,
            'source' => 'manual',
            'category' => 'Informe',
            'is_overdue' => false,
            'registry_code' => 'REP-' . $r->id,
            'created_at' => $r->created_at,
            'inspection' => null,
            'frequency_label' => 'Manual',
            'periodo' => $r->periodo // Agregado para vista especializada
        ]);

        $manualTrainings = $trainingsQuery->get()->map(fn($t) => (object)[
            'id' => $t->id,
            'dynamic_checklist_id' => $t->dynamic_checklist_id,
            'checklist' => (object)['title' => $t->title, 'registry_code' => 'CAP-' . $t->id],
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
            'duration' => $t->duration, // Agregado
            'type' => $t->type // Agregado
        ]);

        // 2. Annual Plan Tasks (Contratos)
        $contractQuery = \App\Models\AnnualPlan::with(['checklist', 'sede', 'sede.empresa'])
            ->whereIn('sede_id', $user->sedes->pluck('id'))
            ->where($month, true)
            ->where('year', now()->year)
            ->when($this->search, function ($q) {
                $q->where(function($sub) {
                    $sub->whereHas('checklist', function ($c) {
                        $c->where('title', 'like', '%' . $this->search . '%');
                    })->orWhere('activity_name', 'like', '%' . $this->search . '%');
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
                if ($this->statusFilter !== 'pending') {
                    $q->whereRaw('1 = 0');
                }
            });

        $contract = $contractQuery->get()->map(fn($p) => (object)[
            'id' => null,
            'dynamic_checklist_id' => $p->checklist_id,
            'checklist' => $p->checklist ?? (object)['title' => $p->activity_name, 'registry_code' => $p->activity_code],
            'sede' => $p->sede,
            'empresa' => $p->sede?->empresa,
            'due_date' => now()->endOfMonth(),
            'status' => 'pending',
            'source' => 'contract',
            'category' => $p->category, // 'Inspección', 'Capacitación', 'Informe'
            'is_overdue' => false,
            'registry_code' => $p->checklist?->registry_code ?? $p->activity_code,
            'created_at' => $p->created_at,
            'inspection' => null,
            'frequency_label' => $p->frequency === 'none' ? 'Mensual' : ucfirst($p->frequency),
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

        // Fetch filter data
        $empresas = Empresa::whereHas('checklistAssignments', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->orWhereIn('id', $user->sedes->pluck('empresa_id'))->orderBy('nombre')->get();

        $sedes = $this->selectedEmpresa
            ? Sede::where('empresa_id', $this->selectedEmpresa)
                ->whereIn('id', $user->sedes->pluck('id'))
                ->orderBy('nombre_sede')->get()
            : collect();

        return view('livewire.technician.my-assignments', [
            'assignments' => $paginated,
            'inspectionsCount' => $allInspections->count(),
            'trainingsCount' => $allTrainings->count(),
            'reportsCount' => $allReports->count(),
            'empresas' => $empresas,
            'sedes' => $sedes,
        ])->layout('components.layouts.app');
    }
}
