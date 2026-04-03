<?php

namespace App\Livewire\Technician;

use App\Models\ChecklistAssignment;
use App\Models\Empresa;
use App\Models\Sede;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class MyAssignments extends Component
{
    use WithPagination;
    
    // Nueva Inteligencia Operativa
    public $activeVisit;
    public $strategicKPIs = [];

    public string $search = '';
    public ?int $selectedEmpresa = null;
    public ?int $selectedSede = null;
    public string $statusFilter = '';

    public ?int $selectedMonth = null;

    #[\Livewire\Attributes\Url]
    public string $activeTab = 'inspections';

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedEmpresa' => ['except' => null],
        'selectedSede' => ['except' => null],
        'statusFilter' => ['except' => ''],
        'activeTab' => ['except' => 'inspections'],
        'selectedMonth' => ['except' => null],
    ];

    public function mount()
    {
        $this->selectedMonth = $this->selectedMonth ?? now()->month;
    }

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

    public function updatedSelectedMonth()
    {
        $this->resetPage();
    }

    private function getStrategicKPIs($user)
    {
        $monthName = strtolower(\Carbon\Carbon::create()->month($this->selectedMonth)->format('F'));
        $year = now()->year;

        // 1. Cumplimiento Mensual (Contrato Anual)
        $totalPlan = \App\Models\AnnualPlan::whereIn('sede_id', $user->sedes->pluck('id'))
            ->where($monthName, true)
            ->where('year', $year)
            ->count();
            
        $completedCount = \App\Models\DynamicInspection::where('user_id', $user->id)
            ->whereMonth('completed_at', $this->selectedMonth)
            ->whereYear('completed_at', $year)
            ->count();
            
        $compliance = $totalPlan > 0 ? min(100, round(($completedCount / $totalPlan) * 100)) : 100;

        // 2. Hallazgos Críticos
        $criticalCount = \App\Models\Hallazgo::where('inspector_id', $user->id)
            ->where('criticidad', 'alta')
            ->where('estado', '!=', 'resuelto')
            ->count();

        // 3. Inspecciones del Mes
        $totalMonth = \App\Models\DynamicInspection::where('user_id', $user->id)
            ->whereMonth('completed_at', $this->selectedMonth)
            ->whereYear('completed_at', $year)
            ->count();

        return [
            'compliance' => $compliance,
            'criticalCount' => $criticalCount,
            'totalMonth' => $totalMonth
        ];
    }

    public function render(\App\Services\VisitService $visitService)
    {
        $user = Auth::user();
        $this->activeVisit = $visitService->getActiveVisit($user);
        $this->strategicKPIs = $this->getStrategicKPIs($user);
        
        $monthName = strtolower(\Carbon\Carbon::create()->month($this->selectedMonth)->format('F'));

        // 1. Manual Assignments (Solicitudes)
        $manualQuery = ChecklistAssignment::with(['checklist', 'sede', 'empresa', 'visitCompletion.visit'])
            ->where('user_id', $user->id)
            ->when($this->selectedMonth, function($q) {
                $q->whereMonth('due_date', $this->selectedMonth);
            })
            ->whereYear('due_date', now()->year)
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
            ->when($this->selectedMonth, function($q) {
                $q->whereMonth('date', $this->selectedMonth);
            })
            ->whereYear('date', now()->year)
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
            ->when($this->selectedMonth, function($q) {
                $q->whereMonth('created_at', $this->selectedMonth);
            })
            ->whereYear('created_at', now()->year)
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
            'frequency_label' => method_exists($a, 'getFrequencyLabel') ? $a->getFrequencyLabel() : 'Manual',
            'notes' => $a->notes,
            'description' => $a->checklist?->description,
            'trainer_name' => null,
        ]);
        $manualReports = $reportsQuery->get()->map(fn($r) => (object)[
            'id' => $r->id,
            'dynamic_checklist_id' => null,
            'checklist' => (object)['title' => $r->titulo, 'registry_code' => 'REP-' . $r->id],
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
            'periodo' => $r->periodo,
            'notes' => null,
            'description' => null,
            'trainer_name' => null,
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
            'duration' => $t->duration,
            'type' => $t->type,
            'notes' => $t->notes,
            'description' => $t->description,
            'trainer_name' => $t->trainer_name,
        ]);

        // 2. Annual Plan Tasks (Contratos)
        $contractQuery = \App\Models\AnnualPlan::with(['checklist', 'sede', 'sede.empresa'])
            ->whereIn('sede_id', $user->sedes->pluck('id'))
            ->where($monthName, true)
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
                // Si filtra por 'pending', incluimos 'scheduled' (programadas)
                if ($this->statusFilter === 'pending') {
                    $q->whereIn('status', ['pending', 'scheduled']);
                } else {
                    $q->where('status', $this->statusFilter);
                }
            });

        $contract = $contractQuery->get()->map(fn($p) => (object)[
            'id' => null,
            'dynamic_checklist_id' => $p->checklist_id,
            'checklist' => $p->checklist ?? (object)['title' => $p->activity_name, 'registry_code' => $p->activity_code],
            'sede' => $p->sede,
            'empresa' => $p->sede?->empresa,
            'due_date' => \Carbon\Carbon::create(now()->year, $this->selectedMonth)->endOfMonth(),
            'status' => 'scheduled',
            'source' => 'contract',
            'category' => $p->category,
            'is_overdue' => false,
            'registry_code' => $p->checklist?->registry_code ?? $p->activity_code,
            'created_at' => $p->created_at,
            'inspection' => null,
            'frequency_label' => $p->frequency === 'none' ? 'Mensual' : ucfirst($p->frequency),
            'duration' => ($p->category === 'Capacitación') ? '60 min' : null,
            'type' => ($p->category === 'Capacitación') ? 'Presencial' : null,
            'periodo' => ($p->category === 'Informe') ? \Carbon\Carbon::create(now()->year, $this->selectedMonth)->translatedFormat('F Y') : null,
            'notes' => null,
            'description' => $p->checklist?->description,
            'trainer_name' => null,
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
            'activeVisit' => $this->activeVisit,
            'kpis' => $this->strategicKPIs,
        ]);
    }
}
