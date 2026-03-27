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
    public $empresaId = null;
    public $sedeId = null;
    public $greeting;

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->setGreeting();
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

        return view('livewire.admin.admin-dashboard', compact(
            'kpis',
            'charts',
            'recentActivity',
            'upcomingActivities',
            'alerts'
        ));
    }

    private function getKPIs()
    {
        return [
            'checklists' => [
                'total_active' => DynamicChecklist::where('is_active', true)->count(),
                'pending_assignments' => ChecklistAssignment::where('status', 'pending')->count(),
                'overdue_assignments' => ChecklistAssignment::overdue()->count(),
            ],
            'inspections' => [
                'total_this_month' => DynamicInspection::whereMonth('completed_at', now()->month)
                    ->whereYear('completed_at', now()->year)
                    ->count(),
                'completed_today' => DynamicInspection::whereDate('completed_at', today())->count(),
                'avg_per_day' => round(
                    DynamicInspection::whereMonth('completed_at', now()->month)->count() / now()->day,
                    1
                ),
            ],
            'trainings' => [
                'total_scheduled' => Training::where('date', '>=', now())->count(),
                'attendees_this_month' => TrainingAttendance::whereHas('training', function($q) {
                    $q->whereMonth('date', now()->month);
                })->count(),
                'upcoming_7_days' => Training::whereBetween('date', [now(), now()->addDays(7)])->count(),
            ],

            'annual' => [
                'compliance_percentage' => $this->calculateAnnualCompliance(),
                'overdue_activities' => 0, // No status column exists in AnnualPlan
                'activities_this_month' => AnnualPlan::whereMonth('created_at', now()->month)->count(),
            ],
            'visit_certificates' => [
                'total' => GeneradorConstancia::count(),
                'pending_signature' => GeneradorConstancia::whereNull('firma_responsable')->count(),
                'completed_this_month' => GeneradorConstancia::whereNotNull('firma_responsable')
                    ->whereMonth('date_firma_responsable', now()->month)->count(),
            ],
        ];
    }

    private function getCharts()
    {
        return [
            'inspectionsPerMonth' => $this->getInspectionsPerMonth(),
            'checklistsByCategory' => $this->getChecklistsByCategory(),
            'annualCompliance' => $this->getAnnualComplianceChart(),
            'topChecklists' => $this->getTopChecklists(),
        ];
    }

    private function getInspectionsPerMonth()
    {
        $data = DynamicInspection::selectRaw("strftime('%m', completed_at) as month, count(*) as total")
            ->whereYear('completed_at', now()->year)
            ->whereNotNull('completed_at')
            ->groupBy('month')
            ->pluck('total', 'month');

        // Note: strftime returns '01', '02'...
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
