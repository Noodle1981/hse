<?php

namespace App\Livewire\Technician;

use App\Models\{
    DynamicInspection,
    ChecklistAssignment,
    Training,
    TrainingAttendance,
    EppDelivery,
    RiskEvaluation
};
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\{Layout, Title};

#[Layout('components.layouts.app')]
#[Title('Mi Dashboard')]
class TechnicianDashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        
        $kpis = $this->getPersonalKPIs($user);
        $charts = $this->getPersonalCharts($user);
        $myTasks = $this->getMyTasks($user);
        $myAlerts = $this->getMyAlerts($user);

        return view('livewire.technician.technician-dashboard', compact(
            'kpis',
            'charts',
            'myTasks',
            'myAlerts'
        ));
    }

    private function getPersonalKPIs($user)
    {
        return [
            'my_tasks' => [
                'pending' => ChecklistAssignment::where('user_id', $user->id)
                    ->where('status', 'pending')->count(),
                'in_progress' => ChecklistAssignment::where('user_id', $user->id)
                    ->where('status', 'in_progress')->count(),
                'overdue' => ChecklistAssignment::where('user_id', $user->id)
                    ->overdue()->count(),
            ],
            'my_inspections' => [
                'this_month' => DynamicInspection::where('user_id', $user->id)
                    ->whereMonth('completed_at', now()->month)
                    ->count(),
                'today' => DynamicInspection::where('user_id', $user->id)
                    ->whereDate('completed_at', today())->count(),
                'total' => DynamicInspection::where('user_id', $user->id)->count(),
            ],
            'my_trainings' => [
                'attended_this_month' => TrainingAttendance::where('employee_name', 'LIKE', "%{$user->nombres}%")
                    ->whereHas('training', function($q) {
                        $q->whereMonth('date', now()->month);
                    })->count(),
                'upcoming' => Training::where('date', '>=', now())
                    ->whereHas('sede', function($q) use ($user) {
                        $q->whereIn('id', $user->sedes->pluck('id'));
                    })->count(),
            ],
            'my_epp_deliveries' => [
                'this_month' => EppDelivery::where('user_id', $user->id)
                    ->whereMonth('delivery_date', now()->month)->count(),
                'total' => EppDelivery::where('user_id', $user->id)->count(),
            ],
            'my_risk_evaluations' => [
                'pending' => RiskEvaluation::where('user_id', $user->id)
                    ->whereNull('evaluation_date')->count(),
                'this_month' => RiskEvaluation::where('user_id', $user->id)
                    ->whereMonth('evaluation_date', now()->month)->count(),
            ],
        ];
    }

    private function getPersonalCharts($user)
    {
        return [
            'myInspectionsPerMonth' => $this->getMyInspectionsPerMonth($user),
            'myProductivity' => $this->getMyProductivity($user),
        ];
    }

    private function getMyInspectionsPerMonth($user)
    {
        // Compatible con SQLite y MySQL
        $data = DynamicInspection::where('user_id', $user->id)
            ->whereYear('completed_at', now()->year)
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

    private function getMyProductivity($user)
    {
        // Últimos 7 días - Compatible con SQLite y MySQL
        $startDate = now()->subDays(6)->startOfDay();
        $data = DynamicInspection::where('user_id', $user->id)
            ->where('completed_at', '>=', $startDate)
            ->whereNotNull('completed_at')
            ->get()
            ->groupBy(function($item) {
                return $item->completed_at->format('Y-m-d');
            })
            ->map(function($items) {
                return $items->count();
            });

        $labels = [];
        $values = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d/m');
            $values[] = $data[$date] ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $values,
        ];
    }

    private function getMyTasks($user)
    {
        return ChecklistAssignment::with(['checklist:id,title', 'sede:id,nombre_sede'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('due_date')
            ->limit(10)
            ->get();
    }

    private function getMyAlerts($user)
    {
        return [
            'overdue_tasks' => ChecklistAssignment::where('user_id', $user->id)
                ->overdue()->count(),
            'pending_evaluations' => RiskEvaluation::where('user_id', $user->id)
                ->whereNull('evaluation_date')->count(),
            'tasks_due_soon' => ChecklistAssignment::where('user_id', $user->id)
                ->where('status', 'pending')
                ->whereBetween('due_date', [now(), now()->addDays(3)])
                ->count(),
        ];
    }
}
