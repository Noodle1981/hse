<?php

namespace App\Livewire\Technician;

use App\Models\{
    DynamicInspection,
    ChecklistAssignment,
    Training,
    TrainingAttendance,
    GeneradorConstancia
};
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\{Layout, Title};

#[Layout('components.layouts.app')]
#[Title('Mi Dashboard')]
class TechnicianDashboard extends Component
{
    public $misionActiva;
    public $proximasMisiones;
    public $proximasCapacitaciones;

    public function render()
    {
        $user = auth()->user();
        
        $this->misionActiva = $this->getActiveMission($user);
        $this->proximasMisiones = $this->getUpcomingMissions($user);
        $this->proximasCapacitaciones = $this->getUpcomingTrainings($user);
        
        $kpis = $this->getPersonalKPIs($user);
        $charts = $this->getPersonalCharts($user);
        $myAlerts = $this->getMyAlerts($user);

        return view('livewire.technician.technician-dashboard', [
            'kpis' => $kpis,
            'charts' => $charts,
            'myAlerts' => $myAlerts,
            'misionActiva' => $this->misionActiva,
            'proximasMisiones' => $this->proximasMisiones,
            'proximasCapacitaciones' => $this->proximasCapacitaciones,
        ]);
    }

    private function getActiveMission($user)
    {
        return \App\Models\VisitCertificate::with(['sede', 'empresa'])
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();
    }

    private function getUpcomingMissions($user)
    {
        $all = ChecklistAssignment::with(['checklist:id,title,category_id', 'checklist.categoryRelation:id,name', 'sede:id,nombre_sede'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('due_date')
            ->get();

        return [
            'hoy' => $all->filter(fn($t) => $t->due_date?->isToday()),
            'semana' => $all->filter(fn($t) => $t->due_date?->isAfter(now()) && $t->due_date?->isBefore(now()->addDays(7))),
            'vencidas' => $all->filter(fn($t) => $t->isOverdue()),
            'otras' => $all->filter(fn($t) => $t->due_date?->isAfter(now()->addDays(7)) && !$t->isOverdue()),
        ];
    }

    private function getUpcomingTrainings($user)
    {
        return Training::with(['sede', 'empresa'])
            ->where('date', '>=', now())
            ->whereHas('sede', function($q) use ($user) {
                // Si el técnico tiene sedes asociadas (asumiendo relación o filtro por empresa)
                // Por ahora filtramos por las del usuario si tiene la relación sedeUser
                $q->whereIn('id', $user->sedes->pluck('id'));
            })
            ->orderBy('date')
            ->limit(3)
            ->get();
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

            'my_visit_certificates' => [
                'this_month' => GeneradorConstancia::where('user_id', $user->id)
                    ->whereMonth('created_at', now()->month)->count(),
                'pending_signature' => GeneradorConstancia::where('user_id', $user->id)
                    ->whereNull('firma_responsable')->count(),
                'total' => GeneradorConstancia::where('user_id', $user->id)->count(),
            ],

            'my_findings' => [
                'total' => \App\Models\Hallazgo::where('inspector_id', $user->id)->count(),
                'pending' => \App\Models\Hallazgo::where('inspector_id', $user->id)
                    ->where('estado', '!=', 'resuelto')->count(),
                'critical' => \App\Models\Hallazgo::where('inspector_id', $user->id)
                    ->where('criticidad', 'alta')->where('estado', '!=', 'resuelto')->count(),
            ],

            'my_incidents' => [
                'total' => \App\Models\Incidente::where('inspector_id', $user->id)->count(),
                'this_month' => \App\Models\Incidente::where('inspector_id', $user->id)
                    ->whereMonth('fecha', now()->month)->count(),
            ],

            'my_reports' => [
                'total_master' => \App\Models\MasterReport::where('autor_id', $user->id)->count(),
                'completed' => \App\Models\MasterReport::where('autor_id', $user->id)
                    ->where('status', 'finalizado')->count(),
                'pending' => \App\Models\MasterReport::where('autor_id', $user->id)
                    ->where('status', '!=', 'finalizado')->count(),
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

    private function getMyAlerts($user)
    {
        return [
            'overdue_tasks' => ChecklistAssignment::where('user_id', $user->id)
                ->overdue()->count(),
            'tasks_due_soon' => ChecklistAssignment::where('user_id', $user->id)
                ->where('status', 'pending')
                ->whereBetween('due_date', [now(), now()->addDays(3)])
                ->count(),
            'active_visit_overdue' => $this->getActiveVisitOverdue($user),
        ];
    }

    private function getActiveVisitOverdue($user)
    {
        $activeVisit = \App\Models\VisitCertificate::where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();
            
        if ($activeVisit && $activeVisit->start_time) {
            $threshold = 30; // 30 minutos
            if ($activeVisit->start_time->diffInMinutes(now()) >= $threshold) {
                return $activeVisit;
            }
        }
        
        return null;
    }
}
