<?php

namespace App\Livewire\Admin;

use App\Models\{
    DynamicChecklist,
    DynamicInspection,
    ChecklistAssignment,
    Training,
    TrainingAttendance,
    EppDelivery,
    Employee,
    EppItem,
    Risk,
    RiskEvaluation,
    AnnualPlan
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

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function render()
    {
        $kpis = $this->getKPIs();
        $charts = $this->getCharts();
        $recentActivity = $this->getRecentActivity();
        $alerts = $this->getAlerts();

        return view('livewire.admin.admin-dashboard', compact(
            'kpis',
            'charts',
            'recentActivity',
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
            'epp' => [
                'deliveries_this_month' => EppDelivery::whereMonth('delivery_date', now()->month)->count(),
                'active_employees' => Employee::where('is_active', true)->count(),
                'catalog_items' => EppItem::where('is_active', true)->count(),
            ],
            'risks' => [
                'critical_risks' => Risk::whereHas('latestEvaluation', function($q) {
                    $q->where('level', 'Crítico');
                })->count(),
                'high_risks' => Risk::whereHas('latestEvaluation', function($q) {
                    $q->where('level', 'Alto');
                })->count(),
                'pending_evaluations' => Risk::whereDoesntHave('evaluations')->count(),
            ],
            'annual' => [
                'compliance_percentage' => $this->calculateAnnualCompliance(),
                'overdue_activities' => AnnualPlan::where('status', 'overdue')->count(),
                'activities_this_month' => AnnualPlan::whereMonth('created_at', now()->month)->count(),
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
        // Compatible con SQLite y MySQL
        $data = DynamicInspection::whereYear('completed_at', now()->year)
            ->whereNotNull('completed_at')
            ->get()
            ->groupBy(function($item) {
                return $item->completed_at->month;
            })
            ->map(function($items) {
                return $items->count();
            });

        $months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $result = array_fill(0, 12, 0);

        foreach ($data as $month => $total) {
            $result[$month - 1] = $total;
        }

        return [
            'labels' => $months,
            'data' => $result,
        ];
    }

    private function getChecklistsByCategory()
    {
        // Solo obtener checklists que tienen categoría asignada
        $checklists = DynamicChecklist::whereNotNull('category_id')->get();
        
        // Si no hay checklists con categoría, retornar vacío
        if ($checklists->isEmpty()) {
            return [
                'labels' => [],
                'data' => [],
            ];
        }
        
        $data = $checklists->groupBy('category_id')
            ->map(function($items, $categoryId) {
                // Cargar categoría
                $category = \App\Models\Category::find($categoryId);
                $categoryName = $category ? $category->name : 'Desconocida';
                
                return [
                    'category' => $categoryName,
                    'total' => $items->count()
                ];
            })
            ->values();

        return [
            'labels' => $data->pluck('category')->toArray(),
            'data' => $data->pluck('total')->toArray(),
        ];
    }

    private function getAnnualComplianceChart()
    {
        // Simplificado - solo contar actividades planificadas vs completadas por mes
        $months = range(1, 12);
        $planned = [];
        $completed = [];

        foreach ($months as $month) {
            $planned[] = AnnualPlan::whereMonth('created_at', $month)->count();
            $completed[] = AnnualPlan::whereMonth('created_at', $month)
                ->where('status', 'completed')->count();
        }

        return [
            'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            'planned' => $planned,
            'completed' => $completed,
        ];
    }

    private function getTopChecklists()
    {
        $inspections = DynamicInspection::all();
        
        // Si no hay inspecciones, retornar vacío
        if ($inspections->isEmpty()) {
            return [
                'labels' => [],
                'data' => [],
            ];
        }
        
        $data = $inspections->groupBy('dynamic_checklist_id')
            ->map(function($items) {
                $firstItem = $items->first();
                // Cargar checklist manualmente
                $checklistTitle = 'N/A';
                if ($firstItem->dynamic_checklist_id) {
                    $checklist = DynamicChecklist::find($firstItem->dynamic_checklist_id);
                    $checklistTitle = $checklist ? $checklist->title : 'N/A';
                }
                
                return [
                    'checklist' => $checklistTitle,
                    'total' => $items->count()
                ];
            })
            ->sortByDesc('total')
            ->take(5)
            ->values();

        return [
            'labels' => $data->pluck('checklist')->toArray(),
            'data' => $data->pluck('total')->toArray(),
        ];
    }

    private function getRecentActivity()
    {
        return [
            'inspections' => DynamicInspection::with(['checklist:id,title', 'user:id,nombres,apellidos', 'sede:id,nombre_sede'])
                ->latest()
                ->limit(10)
                ->get(),
            'trainings' => Training::with(['sede:id,nombre_sede'])
                ->where('date', '>=', now())
                ->orderBy('date')
                ->limit(5)
                ->get(),
        ];
    }

    private function getAlerts()
    {
        return [
            'overdue_assignments' => ChecklistAssignment::overdue()->count(),
            'critical_risks_unevaluated' => Risk::whereDoesntHave('evaluations')
                ->whereHas('matrix', function($q) {
                    $q->where('status', 'active');
                })->count(),
            'overdue_annual_activities' => AnnualPlan::where('status', 'overdue')->count(),
        ];
    }

    private function calculateAnnualCompliance()
    {
        $total = AnnualPlan::whereYear('created_at', now()->year)->count();
        if ($total == 0) return 0;

        $completed = AnnualPlan::whereYear('created_at', now()->year)
            ->where('status', 'completed')->count();

        return round(($completed / $total) * 100, 1);
    }
}
