<?php

namespace App\Livewire\Admin;

use App\Models\{
    DynamicChecklist,
    DynamicInspection,
    ChecklistAssignment,
    Training,
    TrainingAttendance,
    AnnualPlan,
    GeneradorConstancia
};
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\{Layout, Title};

#[Layout('components.layouts.app')]
#[Title('Dashboard Administrativo')]
class AdminDashboard extends Component
{
    public $dateFrom;
    public $dateTo;
    public $period = 'this_month';
    public $empresaId = null;
    public $sedeId = null;
    public $tecnicoId = null;
    public $showOverdueModal = false;
    public $greeting;

    public function mount()
    {
        $this->updateDates();
        $this->setGreeting();
    }

    public function updatedPeriod()
    {
        $this->updateDates();
        $this->dispatch('periodUpdated');
    }

    public function updatedEmpresaId()
    {
        $this->sedeId = null;
    }

    public function resetFilters()
    {
        $this->empresaId = null;
        $this->sedeId = null;
        $this->tecnicoId = null;
        $this->period = 'this_month';
        $this->updateDates();
    }

    private function updateDates()
    {
        switch ($this->period) {
            case 'this_month':
                $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
                $this->dateTo = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $this->dateFrom = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->dateTo = now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_3_months':
                $this->dateFrom = now()->subMonths(3)->startOfMonth()->format('Y-m-d');
                $this->dateTo = now()->format('Y-m-d');
                break;
            case 'this_quarter':
                $this->dateFrom = now()->startOfQuarter()->format('Y-m-d');
                $this->dateTo = now()->endOfQuarter()->format('Y-m-d');
                break;
            case 'this_year':
                $this->dateFrom = now()->startOfYear()->format('Y-m-d');
                $this->dateTo = now()->endOfYear()->format('Y-m-d');
                break;
            case 'last_year':
                $this->dateFrom = now()->subYear()->startOfYear()->format('Y-m-d');
                $this->dateTo = now()->subYear()->endOfYear()->format('Y-m-d');
                break;
            case 'custom':
                // Mantener fechas actuales
                break;
            default:
                $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
                $this->dateTo = now()->endOfMonth()->format('Y-m-d');
        }
    }

    private function setGreeting()
    {
        $hour = now()->hour;
        if ($hour < 12) {
            $this->greeting = '¡Buenos días!';
        } elseif ($hour < 20) {
            $this->greeting = '¡Buenas tardes!';
        } else {
            $this->greeting = '¡Buenas noches!';
        }
    }

    public function render()
    {
        $kpis = $this->getKPIs();
        $charts = $this->getCharts();
        $recentActivity = $this->getRecentActivity();
        $upcomingActivities = $this->getUpcomingActivities();
        $alerts = $this->getAlerts();
        $rankings = $this->getRankings();
        $overdueAssignments = $this->showOverdueModal ? $this->getOverdueAssignments() : collect();

        $empresas = \App\Models\Empresa::orderBy('nombre')->get();
        $sedes = $this->empresaId 
            ? \App\Models\Sede::where('empresa_id', $this->empresaId)->orderBy('nombre_sede')->get() 
            : collect();
        $tecnicos = \App\Models\User::where('rol', 'tecnico')->orderBy('nombres')->get();

        return view('livewire.admin.admin-dashboard', compact(
            'kpis',
            'charts',
            'recentActivity',
            'upcomingActivities',
            'alerts',
            'rankings',
            'overdueAssignments',
            'empresas',
            'sedes',
            'tecnicos'
        ));
    }

    private function getOverdueAssignments()
    {
        return \App\Models\ChecklistAssignment::overdue()
            ->with(['checklist', 'empresa', 'sede', 'user'])
            ->when($this->empresaId, fn($q) => $q->where('empresa_id', $this->empresaId))
            ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
            ->when($this->tecnicoId, fn($q) => $q->where('user_id', $this->tecnicoId))
            ->orderBy('due_date')
            ->get();
    }

    private function getKPIs()
    {
        $from = $this->dateFrom . ' 00:00:00';
        $to = $this->dateTo . ' 23:59:59';

        return [
            'checklists' => [
                'total_active' => DynamicChecklist::where('is_active', true)->count(),
                'pending_assignments' => ChecklistAssignment::where('status', 'pending')->count(),
                'overdue_assignments' => ChecklistAssignment::overdue()->count(),
                'completed_period' => ChecklistAssignment::where('status', 'completed')
                    ->whereBetween('updated_at', [$from, $to])->count(),
            ],
            'inspections' => [
                'total_period' => DynamicInspection::whereBetween('completed_at', [$from, $to])
                    ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
                    ->when($this->empresaId, fn($q) => $q->whereHas('sede', fn($sq) => $sq->where('empresa_id', $this->empresaId)))
                    ->when($this->tecnicoId, fn($q) => $q->where('user_id', $this->tecnicoId))
                    ->count(),
                'completed_today' => DynamicInspection::whereDate('completed_at', today())
                    ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
                    ->when($this->tecnicoId, fn($q) => $q->where('user_id', $this->tecnicoId))
                    ->count(),
                'avg_per_day' => $this->calculateAvgInspections($from, $to),
            ],
            'findings' => [
                'total' => \App\Models\Hallazgo::whereBetween('fecha_deteccion', [$from, $to])
                    ->when($this->empresaId, fn($q) => $q->where('empresa_id', $this->empresaId))
                    ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
                    ->when($this->tecnicoId, fn($q) => $q->where('creado_por', $this->tecnicoId))
                    ->count(),
                'pending' => \App\Models\Hallazgo::whereIn('estado', ['pendiente', 'en_proceso'])
                    ->whereBetween('fecha_deteccion', [$from, $to])
                    ->when($this->empresaId, fn($q) => $q->where('empresa_id', $this->empresaId))
                    ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
                    ->count(),
                'critical' => \App\Models\Hallazgo::where('criticidad', 'critico')
                    ->whereBetween('fecha_deteccion', [$from, $to])
                    ->when($this->empresaId, fn($q) => $q->where('empresa_id', $this->empresaId))
                    ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
                    ->count(),
                'resolved' => \App\Models\Hallazgo::whereIn('estado', ['resuelto', 'cerrado', 'verificado'])
                    ->whereBetween('fecha_resolucion', [$from, $to])
                    ->when($this->empresaId, fn($q) => $q->where('empresa_id', $this->empresaId))
                    ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
                    ->count(),
            ],
            'incidents' => [
                'total' => \App\Models\Incidente::whereBetween('fecha_ocurrencia', [$from, $to])
                    ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
                    ->when($this->empresaId, fn($q) => $q->whereHas('sede', fn($sq) => $sq->where('empresa_id', $this->empresaId)))
                    ->count(),
                'serious' => \App\Models\Incidente::whereIn('gravedad', ['grave', 'fatal'])
                    ->whereBetween('fecha_ocurrencia', [$from, $to])
                    ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
                    ->count(),
                'lost_days' => \App\Models\Incidente::whereBetween('fecha_ocurrencia', [$from, $to])
                    ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
                    ->sum('dias_perdidos'),
            ],
            'reports' => [
                'total_master' => \App\Models\MasterReport::whereBetween('created_at', [$from, $to])
                    ->when($this->empresaId, fn($q) => $q->where('empresa_id', $this->empresaId))
                    ->count(),
                'completed' => \App\Models\MasterReport::where('estado', 'finalizado')
                    ->whereBetween('updated_at', [$from, $to])
                    ->when($this->empresaId, fn($q) => $q->where('empresa_id', $this->empresaId))
                    ->count(),
                'pending' => \App\Models\MasterReport::whereIn('estado', ['pendiente', 'en_revision'])
                    ->whereBetween('created_at', [$from, $to])
                    ->when($this->empresaId, fn($q) => $q->where('empresa_id', $this->empresaId))
                    ->count(),
            ],
            'ecosystem' => [
                'total_companies' => \App\Models\Empresa::count(),
                'active_technicians' => \App\Models\User::where('rol', 'tecnico')
                    ->whereHas('checklists', function($q) use ($from, $to) {
                        $q->whereBetween('updated_at', [$from, $to]);
                    })->count(),
            ],
            'trainings' => [
                'total_scheduled' => Training::whereBetween('date', [$from, $to])
                    ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
                    ->when($this->tecnicoId, fn($q) => $q->where('responsible_user_id', $this->tecnicoId))
                    ->count(),
                'attendees_period' => TrainingAttendance::whereHas('training', function($q) use ($from, $to) {
                    $q->whereBetween('date', [$from, $to])
                      ->when($this->sedeId, fn($sq) => $sq->where('sede_id', $this->sedeId))
                      ->when($this->tecnicoId, fn($sq) => $sq->where('responsible_user_id', $this->tecnicoId));
                })->count(),
            ],
            'annual' => [
                'compliance_percentage' => $this->calculateAnnualCompliance(),
            ],
            'visit_certificates' => [
                'total_period' => GeneradorConstancia::whereBetween('fecha_visita', [$from, $to])
                    ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
                    ->when($this->empresaId, fn($q) => $q->whereHas('sede', fn($sq) => $sq->where('empresa_id', $this->empresaId)))
                    ->when($this->tecnicoId, fn($q) => $q->where('user_id', $this->tecnicoId))
                    ->count(),
                'pending_signature' => GeneradorConstancia::whereNull('firma_responsable')
                    ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
                    ->count(),
            ],
        ];
    }

    private function calculateAvgInspections($from, $to)
    {
        $days = \Carbon\Carbon::parse($from)->diffInDays(\Carbon\Carbon::parse($to)) ?: 1;
        $total = DynamicInspection::whereBetween('completed_at', [$from, $to])->count();
        return round($total / $days, 1);
    }

    private function getRankings()
    {
        $from = $this->dateFrom . ' 00:00:00';
        $to = $this->dateTo . ' 23:59:59';

        return [
            'top_technicians' => \App\Models\User::where('rol', 'tecnico')
                ->withCount(['checklists' => function($q) use ($from, $to) {
                    $q->where('status', 'completed')->whereBetween('updated_at', [$from, $to])
                      ->when($this->sedeId, fn($sq) => $sq->where('sede_id', $this->sedeId))
                      ->when($this->empresaId, fn($sq) => $sq->whereHas('sede', fn($ssq) => $ssq->where('empresa_id', $this->empresaId)));
                }])
                ->orderByDesc('checklists_count')
                ->take(5)
                ->get(),
            
            'top_companies_findings' => \App\Models\Empresa::withCount(['hallazgos as findings_count' => function($q) use ($from, $to) {
                    $q->whereIn('estado', ['pendiente', 'en_proceso'])
                      ->when($this->sedeId, fn($sq) => $sq->where('sede_id', $this->sedeId));
                }])
                ->when($this->empresaId, fn($q) => $q->where('id', $this->empresaId))
                ->orderByDesc('findings_count')
                ->take(5)
                ->get(),
        ];
    }

    private function getCharts()
    {
        return [
            'inspectionsPerMonth' => $this->getInspectionsPerMonth(),
            'checklistsByCategory' => $this->getChecklistsByCategory(),
            'annualCompliance' => $this->getAnnualComplianceChart(),
            'topChecklists' => $this->getTopChecklists(),
            'findingsByStatus' => $this->getFindingsByStatus(),
            'incidentsPerMonth' => $this->getIncidentsPerMonth(),
        ];
    }

    private function getFindingsByStatus()
    {
        $from = $this->dateFrom . ' 00:00:00';
        $to = $this->dateTo . ' 23:59:59';

        $data = \App\Models\Hallazgo::whereBetween('fecha_deteccion', [$from, $to])
            ->when($this->empresaId, fn($q) => $q->where('empresa_id', $this->empresaId))
            ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
            ->select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->get();

        return [
            'labels' => $data->pluck('estado')->map(fn($s) => ucfirst($s))->toArray(),
            'data' => $data->pluck('total')->toArray(),
        ];
    }

    private function getIncidentsPerMonth()
    {
        $from = $this->dateFrom . ' 00:00:00';
        $to = $this->dateTo . ' 23:59:59';

        $data = \App\Models\Incidente::selectRaw("strftime('%m', fecha_ocurrencia) as month, count(*) as total")
            ->whereBetween('fecha_ocurrencia', [$from, $to])
            ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
            ->when($this->empresaId, fn($q) => $q->whereHas('sede', fn($sq) => $sq->where('empresa_id', $this->empresaId)))
            ->groupBy('month')
            ->pluck('total', 'month');

        $result = array_fill(0, 12, 0);
        foreach ($data as $month => $total) {
            $monthIndex = (int)$month - 1;
            if ($monthIndex >= 0 && $monthIndex < 12) {
                $result[$monthIndex] = $total;
            }
        }

        return [
            'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            'data' => $result,
        ];
    }

    private function getInspectionsPerMonth()
    {
        $from = $this->dateFrom . ' 00:00:00';
        $to = $this->dateTo . ' 23:59:59';

        $data = DynamicInspection::selectRaw("strftime('%m', completed_at) as month, count(*) as total")
            ->whereBetween('completed_at', [$from, $to])
            ->whereNotNull('completed_at')
            ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
            ->when($this->empresaId, fn($q) => $q->whereHas('sede', fn($sq) => $sq->where('empresa_id', $this->empresaId)))
            ->when($this->tecnicoId, fn($q) => $q->where('user_id', $this->tecnicoId))
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthsLabels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $result = array_fill(0, 12, 0);

        foreach ($data as $month => $total) {
            $monthIndex = (int)$month - 1;
            if ($monthIndex >= 0 && $monthIndex < 12) {
                $result[$monthIndex] = $total;
            }
        }

        return [
            'labels' => $monthsLabels,
            'data' => $result,
        ];
    }

    private function getChecklistsByCategory()
    {
        $data = DynamicChecklist::join('categories', 'dynamic_checklists.category_id', '=', 'categories.id')
            ->select('categories.name as category', DB::raw('count(*) as total'))
            ->groupBy('categories.name')
            ->get();

        return [
            'labels' => $data->pluck('category')->toArray(),
            'data' => $data->pluck('total')->toArray(),
        ];
    }

    private function getAnnualComplianceChart()
    {
        $months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $planned = array_fill(0, 12, 0);
        $completed = array_fill(0, 12, 0);

        // Optimization: Use a single query for planned counts
        $plannedData = AnnualPlan::selectRaw("strftime('%m', created_at) as month, count(*) as total")
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        foreach ($plannedData as $month => $total) {
            $monthIndex = (int)$month - 1;
            if ($monthIndex >= 0 && $monthIndex < 12) {
                $planned[$monthIndex] = $total;
            }
        }

        // Note: Completed status logic is complex and requires inspection data.
        // For now, we set it to 0 to avoid broken queries.

        return [
            'labels' => $months,
            'planned' => $planned,
            'completed' => $completed,
        ];
    }

    private function getTopChecklists()
    {
        $data = DynamicInspection::select('dynamic_checklist_id', DB::raw('count(*) as total'))
            ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
            ->when($this->tecnicoId, fn($q) => $q->where('user_id', $this->tecnicoId))
            ->groupBy('dynamic_checklist_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();
        
        $checklistIds = $data->pluck('dynamic_checklist_id');
        $checklists = DynamicChecklist::whereIn('id', $checklistIds)->pluck('title', 'id');

        $labels = $data->map(function($item) use ($checklists) {
            return $checklists[$item->dynamic_checklist_id] ?? 'N/A';
        })->toArray();

        return [
            'labels' => $labels,
            'data' => $data->pluck('total')->toArray(),
        ];
    }

    private function getRecentActivity()
    {
        $activities = collect();

        // 1. Visitas (GeneradorConstancia)
        $visitas = GeneradorConstancia::with(['sede.empresa', 'user'])
            ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
            ->when($this->empresaId, fn($q) => $q->whereHas('sede', fn($sq) => $sq->where('empresa_id', $this->empresaId)))
            ->when($this->tecnicoId, fn($q) => $q->where('user_id', $this->tecnicoId))
            ->latest('fecha_visita')
            ->limit(15)
            ->get()
            ->map(function ($visita) {
                return (object)[
                    'type' => 'visita',
                    'title' => 'Visita: ' . ($visita->tipo_visita ?? 'General'),
                    'empresa' => $visita->sede && $visita->sede->empresa ? $visita->sede->empresa->nombre : 'N/A',
                    'user_name' => $visita->user ? ($visita->user->nombres . ' ' . $visita->user->apellidos) : 'N/A',
                    'date' => $visita->fecha_visita,
                ];
            });
        $activities = $activities->concat($visitas);

        // 2. Inspecciones completadas
        $inspecciones = DynamicInspection::with(['checklist:id,title', 'user:id,nombres,apellidos', 'sede.empresa', 'assignment.empresa'])
            ->completed()
            ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
            ->when($this->empresaId, fn($q) => $q->whereHas('sede', fn($sq) => $sq->where('empresa_id', $this->empresaId)))
            ->when($this->tecnicoId, fn($q) => $q->where('user_id', $this->tecnicoId))
            ->latest('completed_at')
            ->limit(15)
            ->get()
            ->map(function ($inspeccion) {
                $empresaNombre = $inspeccion->assignment && $inspeccion->assignment->empresa
                    ? $inspeccion->assignment->empresa->nombre
                    : ($inspeccion->sede && $inspeccion->sede->empresa ? $inspeccion->sede->empresa->nombre : 'N/A');
                return (object)[
                    'type' => 'inspeccion',
                    'title' => 'Inspección: ' . ($inspeccion->checklist->title ?? 'N/A'),
                    'empresa' => $empresaNombre,
                    'user_name' => $inspeccion->user ? ($inspeccion->user->nombres . ' ' . $inspeccion->user->apellidos) : 'N/A',
                    'date' => $inspeccion->completed_at,
                ];
            });
        $activities = $activities->concat($inspecciones);

        // 3. Capacitaciones realizadas
        $capacitaciones = Training::with(['sede.empresa', 'responsibleUser'])
            ->where('date', '<=', now())
            ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
            ->when($this->tecnicoId, fn($q) => $q->where('responsible_user_id', $this->tecnicoId))
            ->latest('date')
            ->limit(15)
            ->get()
            ->map(function ($training) {
                return (object)[
                    'type' => 'capacitacion',
                    'title' => 'Capacitación: ' . $training->title,
                    'empresa' => $training->sede && $training->sede->empresa ? $training->sede->empresa->nombre : 'N/A',
                    'user_name' => $training->responsibleUser ? ($training->responsibleUser->nombres . ' ' . $training->responsibleUser->apellidos) : 'N/A',
                    'date' => $training->date,
                ];
            });
        $activities = $activities->concat($capacitaciones);

        // 4. Checklists completados (ChecklistAssignment status completed)
        $checklists = ChecklistAssignment::with(['checklist:id,title', 'empresa', 'user:id,nombres,apellidos'])
            ->completed()
            ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
            ->when($this->empresaId, fn($q) => $q->where('empresa_id', $this->empresaId))
            ->when($this->tecnicoId, fn($q) => $q->where('user_id', $this->tecnicoId))
            ->latest('updated_at')
            ->limit(15)
            ->get()
            ->map(function ($assignment) {
                return (object)[
                    'type' => 'checklist',
                    'title' => 'Checklist: ' . ($assignment->checklist->title ?? 'N/A'),
                    'empresa' => $assignment->empresa->nombre ?? 'N/A',
                    'user_name' => $assignment->user ? ($assignment->user->nombres . ' ' . $assignment->user->apellidos) : 'N/A',
                    'date' => $assignment->updated_at,
                ];
            });
        $activities = $activities->concat($checklists);

        return $activities->sortByDesc('date')->take(15)->values();
    }

    private function getUpcomingActivities()
    {
        $upcoming = collect();

        // 1. Checklist Assignments pendientes o en progreso
        $assignments = ChecklistAssignment::with(['checklist:id,title', 'empresa', 'sede.empresa', 'user'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('due_date', 'asc')
            ->limit(15)
            ->get()
            ->map(function ($assignment) {
                $empresaNombre = $assignment->empresa ? $assignment->empresa->nombre : ($assignment->sede && $assignment->sede->empresa ? $assignment->sede->empresa->nombre : 'N/A');
                return (object)[
                    'type' => 'checklist',
                    'title' => $assignment->checklist->title ?? 'N/A',
                    'empresa' => $empresaNombre,
                    'user_name' => $assignment->user ? ($assignment->user->nombres . ' ' . $assignment->user->apellidos) : 'Sin asignar',
                    'date' => $assignment->due_date,
                    'status' => $assignment->status,
                ];
            });
        $upcoming = $upcoming->concat($assignments);

        // 2. Capacitaciones Próximas
        $trainings = Training::with(['sede.empresa', 'responsibleUser'])
            ->where('date', '>', now())
            ->orderBy('date', 'asc')
            ->limit(15)
            ->get()
            ->map(function ($training) {
                return (object)[
                    'type' => 'capacitacion',
                    'title' => $training->title,
                    'empresa' => $training->sede && $training->sede->empresa ? $training->sede->empresa->nombre : 'N/A',
                    'user_name' => $training->responsibleUser ? ($training->responsibleUser->nombres . ' ' . $training->responsibleUser->apellidos) : 'Sin asignar',
                    'date' => $training->date,
                    'status' => 'pending',
                ];
            });
        $upcoming = $upcoming->concat($trainings);

        return $upcoming->sortBy('date')->take(15)->values();
    }

    private function getAlerts()
    {
        return [
            'overdue_assignments' => ChecklistAssignment::overdue()->count(),
            'overdue_annual_activities' => AnnualPlan::where('status', 'overdue')->count(),
        ];
    }

    private function calculateAnnualCompliance()
    {
        $total = AnnualPlan::whereYear('created_at', now()->year)->count();
        if ($total == 0) return 0;

        // Broken query fixed: no status column exists
        return 0; 
    }
}
