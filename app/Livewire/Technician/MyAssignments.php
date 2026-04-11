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

    public function updatedSelectedSede($value)
    {
        $this->resetPage();
    }

    public function updatedSelectedMonth()
    {
        $this->resetPage();
    }

    private function getStrategicKPIs($user, $allInspections, $allTrainings, $allReports)
    {
        $year = now()->year;

        // 1. Cumplimiento Operativo (Total de tareas del periodo)
        $totalTasks = $allInspections->count() + $allTrainings->count() + $allReports->count();
        $completedTasks = $allInspections->where('status', 'completed')->count() +
                          $allTrainings->where('status', 'completed')->count() +
                          $allReports->filter(fn($r) => in_array($r->status, ['finalizado', 'completed']))->count();
            
        $compliance = $totalTasks > 0 ? min(100, round(($completedTasks / $totalTasks) * 100)) : 0;

        // 2. Hallazgos Críticos
        $criticalCount = \App\Models\Hallazgo::where('inspector_id', $user->id)
            ->where('criticidad', 'alta')
            ->where('estado', '!=', 'resuelto')
            ->count();

        // 3. Total de Inspecciones Ejecutadas (Solo el número)
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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // IDs de Sedes y Empresas ASIGNADAS al técnico (Fuente única de verdad)
        $assignedSedes = $user->sedes()->select('sedes.id', 'sedes.empresa_id')->get();
        $assignedSedeIds = $assignedSedes->pluck('id')->toArray();
        $assignedEmpresaIds = $assignedSedes->pluck('empresa_id')->unique()->toArray();

        $this->activeVisit = $visitService->getActiveVisit($user);
        
        $monthName = strtolower(\Carbon\Carbon::create()->month($this->selectedMonth)->format('F'));

        // 1. Manual Assignments (Solicitudes)
        $manualQuery = ChecklistAssignment::with(['checklist.categoryRelation', 'checklist.questions', 'sede', 'empresa', 'visitCompletion.visit'])
            ->where('user_id', $user->id)
            ->whereIn('sede_id', $assignedSedeIds)
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
                $q->whereHas('sede', function($sub) {
                    $sub->where('empresa_id', $this->selectedEmpresa);
                });
            })
            ->when($this->selectedSede, function ($q) {
                $q->where('sede_id', $this->selectedSede);
            })
            // EXCLUIR: Asignaciones creadas automáticamente por el sistema para respaldar tareas del plan anual/mensual
            // Esto evita el duplicado "Contrato" vs "Solicitud" en la vista del técnico
            ->where(function($q) {
                $q->whereNull('notes')
                  ->orWhere(function($sq) {
                      $sq->where('notes', 'NOT LIKE', '%Vínculo automático desde Plan Anual%')
                         ->where('notes', 'NOT LIKE', '%Plan Mensual Recomendado%');
                  });
            });

        // 1.1 Manual Trainings (Solicitudes de Capacitación)
        $trainingsQuery = \App\Models\Training::with(['sede', 'empresa', 'dynamicChecklist.categoryRelation', 'dynamicChecklist.questions'])
            ->where('user_id', $user->id)
            ->whereIn('sede_id', $assignedSedeIds)
            ->where('status', '!=', 'completed')
            ->when($this->selectedMonth, function($q) {
                $q->whereMonth('date', $this->selectedMonth);
            })
            ->whereYear('date', now()->year)
            ->when($this->search, function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->selectedEmpresa, function ($q) {
                $q->whereHas('sede', function($sub) {
                    $sub->where('empresa_id', $this->selectedEmpresa);
                });
            })
            ->when($this->selectedSede, function ($q) {
                $q->where('sede_id', $this->selectedSede);
            });

        // 1.2 Manual Reports (Informes Maestros en Borrador)
        $reportsQuery = \App\Models\MasterReport::with(['sede', 'sede.empresa'])
            ->where('user_id', $user->id)
            ->whereIn('sede_id', $assignedSedeIds)
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

        $now = \Carbon\Carbon::now();

        $manual = $manualQuery->get()->map(function($a) use ($now) {
            $dueDate = $a->due_date ? \Carbon\Carbon::parse($a->due_date) : null;
            $daysRemaining = $dueDate ? (int) $now->diffInDays($dueDate, false) : null;
            
            // Cálculos dinámicos para el técnico
            $totalPoints = $a->checklist?->questions->count() ?? 0;
            $criticalPoints = $a->checklist?->questions->filter(fn($q) => !empty($q->failure_values))->count() ?? 0;

            return (object)[
                'id' => $a->id,
                'dynamic_checklist_id' => $a->dynamic_checklist_id,
                'checklist' => $a->checklist,
                'sede' => $a->sede,
                'empresa' => $a->empresa,
                'due_date' => $dueDate,
                'status' => $a->status,
                'source' => 'manual',
                'category' => 'Inspección',
                'is_overdue' => method_exists($a, 'isOverdue') ? $a->isOverdue() : ($dueDate ? $dueDate->isPast() : false),
                'days_remaining' => $daysRemaining,
                'is_warning' => $daysRemaining !== null && $daysRemaining <= 7 && $daysRemaining >= 0 && $a->status !== 'completed',
                'registry_code' => $a->checklist?->registry_code,
                'created_at' => $a->created_at,
                'inspection' => $a->inspection ?? ($a->completion?->inspection ?? null),
                'frequency_label' => method_exists($a, 'getFrequencyLabel') ? $a->getFrequencyLabel() : 'Manual',
                'notes' => $a->notes,
                'description' => $a->checklist?->description,
                'category_name' => $a->checklist?->categoryRelation?->name,
                'category_description' => $a->checklist?->categoryRelation?->description,
                'total_points' => $totalPoints,
                'critical_points' => $criticalPoints,
                'trained_checklist' => null,
                'trainer_name' => null,
            ];
        });

        $manualReports = $reportsQuery->get()->map(function($r) use ($now) {
            $dueDate = $r->created_at->addDays(7);
            $daysRemaining = (int) $now->diffInDays($dueDate, false);
            return (object)[
                'id' => $r->id,
                'dynamic_checklist_id' => null,
                'checklist' => (object)['title' => $r->titulo, 'registry_code' => 'REP-' . $r->id],
                'sede' => $r->sede,
                'empresa' => $r->sede?->empresa,
                'due_date' => $dueDate,
                'status' => $r->estado,
                'source' => 'manual',
                'category' => 'Informe',
                'is_overdue' => $dueDate->isPast() && $r->estado !== 'finalizado',
                'days_remaining' => $daysRemaining,
                'is_warning' => $daysRemaining <= 7 && $daysRemaining >= 0 && $r->estado !== 'finalizado',
                'registry_code' => 'REP-' . $r->id,
                'created_at' => $r->created_at,
                'frequency_label' => 'Manual',
                'periodo' => $r->periodo,
                'periodo_label' => $r->formatted_period,
                'notes' => null,
                'description' => null,
                'category_name' => 'Informe Maestro',
                'category_description' => 'Redacción de informe técnico consolidado del periodo.',
                'total_points' => 0,
                'critical_points' => 0,
                'trained_checklist' => null,
                'trainer_name' => null,
            ];
        });

        $manualTrainings = $trainingsQuery->get()->map(function($t) use ($now) {
            $dueDate = $t->date ? \Carbon\Carbon::parse($t->date) : null;
            $daysRemaining = $dueDate ? (int) $now->diffInDays($dueDate, false) : null;

            // Info del checklist a capacitar
            $trainedChecklistTitle = $t->dynamicChecklist?->title;
            $totalPoints = $t->dynamicChecklist?->questions->count() ?? 0;

            return (object)[
                'id' => $t->id,
                'dynamic_checklist_id' => $t->dynamic_checklist_id,
                'checklist' => (object)['title' => $t->title, 'registry_code' => 'CAP-' . $t->id],
                'sede' => $t->sede,
                'empresa' => $t->empresa,
                'due_date' => $dueDate,
                'status' => $t->status,
                'source' => 'manual',
                'category' => 'Capacitación',
                'is_overdue' => $dueDate ? ($dueDate->isPast() && $t->status !== 'completed') : false,
                'days_remaining' => $daysRemaining,
                'is_warning' => $daysRemaining !== null && $daysRemaining <= 7 && $daysRemaining >= 0 && $t->status !== 'completed',
                'registry_code' => 'CAP-' . $t->id,
                'created_at' => $t->created_at,
                'inspection' => null,
                'frequency_label' => 'Manual',
                'duration' => $t->duration,
                'type' => $t->type,
                'notes' => $t->notes,
                'description' => $t->description,
                'category_name' => $t->dynamicChecklist?->categoryRelation?->name ?? 'Capacitación',
                'category_description' => $t->dynamicChecklist?->categoryRelation?->description,
                'trained_checklist' => $trainedChecklistTitle,
                'total_points' => $totalPoints,
                'trainer_name' => $t->trainer_name,
            ];
        });

        // 2. Annual Plan Tasks (Contratos)
        $queryMonth = now()->year . '-' . str_pad($this->selectedMonth, 2, '0', STR_PAD_LEFT);
        
        $contractQuery = \App\Models\AnnualPlan::with(['checklist.categoryRelation', 'checklist.questions', 'sede', 'sede.empresa'])
            ->whereIn('sede_id', $assignedSedeIds)
            ->where('scheduled_months', 'like', '%"' . $queryMonth . '"%')
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
            });

        // Optimization: Fetch all completed inspections for this user/month to map statuses
        $completedInspections = \App\Models\DynamicInspection::where('user_id', $user->id)
            ->whereMonth('completed_at', $this->selectedMonth)
            ->whereYear('completed_at', now()->year)
            ->where('status', 'completed')
            ->get()
            ->groupBy(function($i) {
                return $i->sede_id . '_' . $i->dynamic_checklist_id;
            });

        // Fetch completed trainings for this user/month to map contract training statuses
        $completedTrainings = \App\Models\Training::where('user_id', $user->id)
            ->whereMonth('updated_at', $this->selectedMonth)
            ->whereYear('updated_at', now()->year)
            ->where('status', 'completed')
            ->get()
            ->groupBy(function($t) {
                return $t->sede_id . '_' . strtolower(trim($t->title));
            });

        $contract = $contractQuery->get()->map(function($p) use ($now, $completedInspections, $completedTrainings) {
            $monthEndDate = \Carbon\Carbon::create(now()->year, $this->selectedMonth)->endOfMonth();

            // Use specific execution date if the admin set one, otherwise default to end of month
            $scheduledDates = $p->scheduled_dates ?? [];
            $queryMonth     = now()->year . '-' . str_pad($this->selectedMonth, 2, '0', STR_PAD_LEFT);
            $specificDate   = isset($scheduledDates[$queryMonth])
                ? \Carbon\Carbon::parse($scheduledDates[$queryMonth])
                : null;
            $effectiveDueDate = $specificDate ?? $monthEndDate;

            // BUSCAR ASIGNACIÓN ACTIVA: Ver si hay una ChecklistAssignment para este plan/checklist/sede
            // Esto nos da el estado real (in_progress, etc) antes de que sea una inspección terminada
            $activeAssignment = \App\Models\ChecklistAssignment::where('user_id', Auth::id())
                ->where('sede_id', $p->sede_id)
                ->where('dynamic_checklist_id', $p->checklist_id)
                ->where(function($q) {
                    $q->where('notes', 'LIKE', '%Vínculo automático desde Plan Anual%')
                      ->orWhere('notes', 'LIKE', '%Plan Mensual Recomendado%');
                })
                ->latest()
                ->first();

            // Check if there is a completed inspection for this plan/checklist/sede
            $key = $p->sede_id . '_' . $p->checklist_id;
            $inspection = isset($completedInspections[$key]) ? $completedInspections[$key]->first() : null;

            // Check if there is a completed training for this plan task
            $trainingKey = $p->sede_id . '_' . strtolower(trim($p->activity_name));
            $completedTraining = ($p->category === 'Capacitación') 
                ? (isset($completedTrainings[$trainingKey]) ? $completedTrainings[$trainingKey]->first() : null)
                : null;

            $status = 'scheduled';
            if ($inspection || $completedTraining) {
                $status = 'completed';
            } elseif ($activeAssignment) {
                $status = $activeAssignment->status; // 'in_progress' o 'pending'
            }
            // Detección de Informes
            $reportId = null;
            $periodo = null;
            if ($p->category === 'Informe') {
                $periodo = now()->year . '-' . str_pad($this->selectedMonth, 2, '0', STR_PAD_LEFT);
                $existingReport = \App\Models\MasterReport::where('sede_id', $p->sede_id)
                    ->where('periodo', $periodo)
                    ->first();
                $reportId = $existingReport?->id;
                
                if ($existingReport) {
                    $status = ($existingReport->estado === 'finalizado') ? 'completed' : 'in_progress';
                }
            }

            $dueDate = $inspection ? $inspection->completed_at : $effectiveDueDate;
            $daysRemaining = (int) $now->diffInDays($effectiveDueDate, false);

            // Cálculos dinámicos para el técnico
            $totalPoints = $p->checklist?->questions->count() ?? 0;
            $criticalPoints = $p->checklist?->questions->filter(fn($q) => !empty($q->failure_values))->count() ?? 0;

            return (object)[
                'id' => $p->id,
                'dynamic_checklist_id' => $p->checklist_id,
                'checklist' => $p->checklist ?? (object)['title' => $p->activity_name, 'registry_code' => $p->activity_code],
                'sede' => $p->sede,
                'empresa' => $p->sede?->empresa,
                'due_date' => $dueDate,
                'status' => $status,
                'source' => 'contract',
                'category' => $p->category,
                'is_overdue' => !$inspection && $effectiveDueDate->isPast(),
                'days_remaining' => $daysRemaining,
                'is_warning' => $daysRemaining <= 7 && $daysRemaining >= 0 && $status !== 'completed',
                'registry_code' => $p->checklist?->registry_code ?? $p->activity_code,
                'created_at' => $p->created_at,
                'inspection' => $inspection ?? $completedTraining,
                'report_id' => $reportId,
                'task_id' => $p->id,
                'periodo' => $periodo,
                'periodo_label' => \Carbon\Carbon::create(now()->year, $this->selectedMonth)->translatedFormat('F Y'),
                'frequency_label' => $p->frequency === 'none' ? 'Mensual' : ucfirst($p->frequency),
                'duration' => ($p->category === 'Capacitación') ? '60 min' : null,
                'type' => ($p->category === 'Capacitación') ? 'Presencial' : null,
                'notes' => null,
                'description' => $p->checklist?->description,
                'category_name' => $p->checklist?->categoryRelation?->name ?? $p->category,
                'category_description' => $p->checklist?->categoryRelation?->description,
                'total_points' => $totalPoints,
                'critical_points' => $criticalPoints,
                'trained_checklist' => ($p->category === 'Capacitación') ? ($p->checklist?->title ?? $p->activity_name) : null,
                'trainer_name' => null,
            ];
        });

        // Merge all into categorized collections (con manejo defensivo de nulos)
        $allInspections = $manual->concat($contract->where('category', 'Inspección'))->sortByDesc('created_at');
        $allTrainings = $manualTrainings->concat($contract->where('category', 'Capacitación'))->sortByDesc('created_at');
        $allReports = $manualReports->concat($contract->where('category', 'Informe'))->sortByDesc('created_at');

        // Filtrado Post-Procesado por Estado (CRÍTICO para contratos dinámicos)
        if ($this->statusFilter) {
            $filter = function($item) {
                return match($this->statusFilter) {
                    'programada' => $item->status === 'scheduled',
                    'solicitada' => $item->status === 'pending',
                    'en_curso' => in_array($item->status, ['in_progress', 'borrador', 'editable']),
                    'finalizada' => in_array($item->status, ['completed', 'finalizado', 'finalizada']),
                    default => $item->status === $this->statusFilter
                };
            };
            $allInspections = $allInspections->filter($filter);
            $allTrainings = $allTrainings->filter($filter);
            $allReports = $allReports->filter($filter);
        }

        // Recalcular KPIs con la data unificada
        $this->strategicKPIs = $this->getStrategicKPIs($user, $allInspections, $allTrainings, $allReports);

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

        // Fetch filter data - RESTRINGIDO A DATOS DEL TÉCNICO
        $empresas = Empresa::whereIn('id', $assignedEmpresaIds)->orderBy('nombre')->get();

        $sedes = Sede::whereIn('id', $assignedSedeIds)
            ->when($this->selectedEmpresa, fn($q) => $q->where('empresa_id', $this->selectedEmpresa))
            ->orderBy('nombre_sede')
            ->get();

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
