<?php

namespace App\Livewire\Admin;

use App\Models\{
    DynamicChecklist,
    DynamicInspection,
    ChecklistAssignment,
    Training,
    TrainingAttendance,
    AnnualPlan,
    VisitCertificate
};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
    public $year;
    public $showOverdueModal = false;
    public $greeting;
    public $efficiency;

    public function mount()
    {
        $this->year = date('Y');
        $this->updateDates();
        $this->setGreeting();
        
        // Default to first available sede if none selected
        if (!$this->sedeId) {
            $this->sedeId = \App\Models\Sede::first()?->id;
        }
    }

    public function updatedPeriod()
    {
        $this->updateDates();
        $this->dispatch('periodUpdated');
    }

    public function updated($property)
    {
        if (in_array($property, ['period', 'empresaId', 'sedeId', 'tecnicoId', 'year'])) {
            $this->dispatch('charts-updated', charts: $this->getCharts());
        }
    }

    public function updatedEmpresaId()
    {
        $this->sedeId = null;
    }

    public function resetFilters()
    {
        $this->empresaId = null;
        $this->sedeId = \App\Models\Sede::first()?->id;
        $this->tecnicoId = null;
        $this->period = 'this_month';
        $this->year = date('Y');
        $this->updateDates();
        $this->dispatch('charts-updated', charts: $this->getCharts());
    }

    // Logic for Strategic Compliance (Ported from AnnualContractDashboard)
    private function calculateEfficiencyMetrics($complianceData)
    {
        $stats = [
            'global' => 0,
            'inspeccion' => 0,
            'capacitacion' => 0,
            'informe' => 0,
            'timelines' => [
                'inspeccion' => array_fill(1, 12, 'none'),
                'capacitacion' => array_fill(1, 12, 'none'),
                'informe' => array_fill(1, 12, 'none'),
            ],
            'counts' => [
                'total' => 0, 'completed' => 0, 'overdue' => 0, 'pending' => 0,
                'insp_total' => 0, 'insp_done' => 0,
                'cap_total' => 0, 'cap_done' => 0,
                'inf_total' => 0, 'inf_done' => 0,
            ]
        ];

        $monthNames = ['january'=>1,'february'=>2,'march'=>3,'april'=>4,'may'=>5,'june'=>6,'july'=>7,'august'=>8,'september'=>9,'october'=>10,'november'=>11,'december'=>12];

        foreach ($complianceData as $planId => $months) {
            $plan = \App\Models\AnnualPlan::find($planId);
            if (!$plan) continue;
            
            $cat = strtolower($plan->category);
            $catKey = ($cat === 'inspección') ? 'inspeccion' : (($cat === 'capacitación') ? 'capacitacion' : 'informe');

            foreach ($months as $monthName => $data) {
                if ($data['status'] === 'none') continue;
                $monthNum = $monthNames[$monthName];

                $stats['counts']['total']++;
                if ($data['status'] === 'completed') $stats['counts']['completed']++;
                if ($data['status'] === 'overdue') $stats['counts']['overdue']++;
                if ($data['status'] === 'pending') $stats['counts']['pending']++;

                // Aggregating for Pillar (category) counts
                if ($catKey === 'inspeccion') {
                    $stats['counts']['insp_total']++;
                    if ($data['status'] === 'completed') $stats['counts']['insp_done']++;
                } elseif ($catKey === 'capacitacion') {
                    $stats['counts']['cap_total']++;
                    if ($data['status'] === 'completed') $stats['counts']['cap_done']++;
                } elseif ($catKey === 'informe') {
                    $stats['counts']['inf_total']++;
                    if ($data['status'] === 'completed') $stats['counts']['inf_done']++;
                }

                // Update Timeline for Pillar: Priority Overdue > Pending > Completed
                $currentStatus = $stats['timelines'][$catKey][$monthNum];
                if ($data['status'] === 'overdue') {
                    $stats['timelines'][$catKey][$monthNum] = 'overdue';
                } elseif ($data['status'] === 'pending' && $currentStatus !== 'overdue') {
                    $stats['timelines'][$catKey][$monthNum] = 'pending';
                } elseif ($data['status'] === 'completed' && $currentStatus === 'none') {
                    $stats['timelines'][$catKey][$monthNum] = 'completed';
                }
            }
        }

        $stats['global'] = $stats['counts']['total'] > 0 ? round(($stats['counts']['completed'] / $stats['counts']['total']) * 100) : 0;
        $stats['inspeccion'] = $stats['counts']['insp_total'] > 0 ? round(($stats['counts']['insp_done'] / $stats['counts']['insp_total']) * 100) : 0;
        $stats['capacitacion'] = $stats['counts']['cap_total'] > 0 ? round(($stats['counts']['cap_done'] / $stats['counts']['cap_total']) * 100) : 0;
        $stats['informe'] = $stats['counts']['inf_total'] > 0 ? round(($stats['counts']['inf_done'] / $stats['counts']['inf_total']) * 100) : 0;
        
        return $stats;
    }

    private function calculateComplianceData($plans)
    {
        $data = [];
        $inspections = \App\Models\DynamicInspection::where('sede_id', $this->sedeId)
            ->whereYear('completed_at', $this->year)->where('status', 'completed')->get()
            ->groupBy(fn($i) => $i->dynamic_checklist_id . '_' . $i->completed_at->month);
        $trainings = \App\Models\Training::where('sede_id', $this->sedeId)
            ->whereYear('date', $this->year)->get()
            ->groupBy(fn($t) => \Carbon\Carbon::parse($t->date ?? $t->created_at)->month);
        $reports = \App\Models\MasterReport::where('sede_id', $this->sedeId)
            ->whereYear('created_at', $this->year)->get()
            ->groupBy(fn($r) => \Carbon\Carbon::now()->month); // Simplified for DB port

        $currentMonth = (int)date('m');
        $months = ['january'=>1, 'february'=>2, 'march'=>3, 'april'=>4, 'may'=>5, 'june'=>6, 'july'=>7, 'august'=>8, 'september'=>9, 'october'=>10, 'november'=>11, 'december'=>12];
        
        foreach ($plans as $plan) {
            $data[$plan->id] = [];
            foreach ($months as $monthName => $monthNumber) {
                if (!$plan->$monthName) { $data[$plan->id][$monthName] = ['status' => 'none', 'count' => 0]; continue; }
                $count = 0;
                if ($plan->category === 'Inspección' && $plan->checklist_id) {
                    $key = $plan->checklist_id . '_' . $monthNumber;
                    $count = isset($inspections[$key]) ? $inspections[$key]->count() : 0;
                } elseif ($plan->category === 'Capacitación') { $count = isset($trainings[$monthNumber]) ? $trainings[$monthNumber]->count() : 0;
                } elseif ($plan->category === 'Informe') { $count = isset($reports[$monthNumber]) ? $reports[$monthNumber]->count() : 0; }
                
                if ($count > 0) $data[$plan->id][$monthName] = ['status' => 'completed', 'count' => $count];
                elseif ($monthNumber < $currentMonth) $data[$plan->id][$monthName] = ['status' => 'overdue', 'count' => 0];
                else $data[$plan->id][$monthName] = ['status' => 'pending', 'count' => 0];
            }
        }
        return $data;
    }

    public function render()
    {
        // Calculate Strategic Multi-pillar Efficiency
        $plans = \App\Models\AnnualPlan::where('sede_id', $this->sedeId)->where('year', $this->year)->get();
        $complianceData = $this->calculateComplianceData($plans);
        $this->efficiency = $this->calculateEfficiencyMetrics($complianceData);
        $efficiency = $this->efficiency;

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
            : \App\Models\Sede::orderBy('nombre_sede')->get();
        $tecnicos = \App\Models\User::where('rol', 'tecnico')->orderBy('nombres')->get();
        $activeSede = \App\Models\Sede::find($this->sedeId);

        return view('livewire.admin.admin-dashboard', compact(
            'kpis', 'charts', 'recentActivity', 'upcomingActivities', 'alerts', 'rankings', 'overdueAssignments',
            'empresas', 'sedes', 'tecnicos', 'efficiency', 'activeSede'
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
                    ->when($this->empresaId, fn($q) => $q->whereHas('sede', fn($sq) => $sq->where('empresa_id', $this->empresaId)))
                    ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
                    ->when($this->tecnicoId, fn($q) => $q->where('user_id', $this->tecnicoId))
                    ->count(),
                'completed' => \App\Models\MasterReport::where('estado', 'finalizado')
                    ->whereBetween('updated_at', [$from, $to])
                    ->when($this->empresaId, fn($q) => $q->whereHas('sede', fn($sq) => $sq->where('empresa_id', $this->empresaId)))
                    ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
                    ->when($this->tecnicoId, fn($q) => $q->where('user_id', $this->tecnicoId))
                    ->count(),
                'pending' => \App\Models\MasterReport::whereIn('estado', ['pendiente', 'en_revision'])
                    ->whereBetween('created_at', [$from, $to])
                    ->when($this->empresaId, fn($q) => $q->whereHas('sede', fn($sq) => $sq->where('empresa_id', $this->empresaId)))
                    ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
                    ->when($this->tecnicoId, fn($q) => $q->where('user_id', $this->tecnicoId))
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
                'total_period' => VisitCertificate::whereBetween('visit_date', [$from, $to])
                    ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
                    ->when($this->empresaId, fn($q) => $q->whereHas('sede', fn($sq) => $sq->where('empresa_id', $this->empresaId)))
                    ->when($this->tecnicoId, fn($q) => $q->where('user_id', $this->tecnicoId))
                    ->count(),
                'pending_signature' => VisitCertificate::whereNull('signature')
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
            ->when($this->tecnicoId, fn($q) => $q->where('user_id', $this->tecnicoId))
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

        $daysDiff = \Carbon\Carbon::parse($from)->diffInDays(\Carbon\Carbon::parse($to));
        $groupByDay = $daysDiff <= 31;

        $query = \App\Models\Incidente::whereBetween('fecha_ocurrencia', [$from, $to])
            ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
            ->when($this->empresaId, fn($q) => $q->whereHas('sede', fn($sq) => $sq->where('empresa_id', $this->empresaId)))
            ->when($this->tecnicoId, fn($q) => $q->where('user_id', $this->tecnicoId));

        if ($groupByDay) {
            $data = (clone $query)->selectRaw("strftime('%Y-%m-%d', fecha_ocurrencia) as date_unit, count(*) as total")
                ->groupBy('date_unit')
                ->pluck('total', 'date_unit');

            $labels = [];
            $result = [];
            $currentDate = \Carbon\Carbon::parse($from);
            $endDate = \Carbon\Carbon::parse($to);

            while ($currentDate <= $endDate) {
                $dateStr = $currentDate->format('Y-m-d');
                $labels[] = $currentDate->format('d/m');
                $result[] = $data[$dateStr] ?? 0;
                $currentDate->addDay();
            }

            return [
                'labels' => $labels,
                'data' => $result,
            ];
        } else {
            $data = (clone $query)->selectRaw("strftime('%m', fecha_ocurrencia) as month, count(*) as total")
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
    }

    private function getInspectionsPerMonth()
    {
        $from = $this->dateFrom . ' 00:00:00';
        $to = $this->dateTo . ' 23:59:59';

        $daysDiff = \Carbon\Carbon::parse($from)->diffInDays(\Carbon\Carbon::parse($to));
        $groupByDay = $daysDiff <= 31;

        $query = DynamicInspection::whereBetween('completed_at', [$from, $to])
            ->whereNotNull('completed_at')
            ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
            ->when($this->empresaId, fn($q) => $q->whereHas('sede', fn($sq) => $sq->where('empresa_id', $this->empresaId)))
            ->when($this->tecnicoId, fn($q) => $q->where('user_id', $this->tecnicoId));

        if ($groupByDay) {
            $data = (clone $query)->selectRaw("strftime('%Y-%m-%d', completed_at) as date_unit, count(*) as total")
                ->groupBy('date_unit')
                ->pluck('total', 'date_unit');

            $labels = [];
            $result = [];
            $currentDate = \Carbon\Carbon::parse($from);
            $endDate = \Carbon\Carbon::parse($to);

            while ($currentDate <= $endDate) {
                $dateStr = $currentDate->format('Y-m-d');
                $labels[] = $currentDate->format('d/m');
                $result[] = $data[$dateStr] ?? 0;
                $currentDate->addDay();
            }

            return [
                'labels' => $labels,
                'data' => $result,
            ];
        } else {
            $data = (clone $query)->selectRaw("strftime('%m', completed_at) as month, count(*) as total")
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
            ->when($this->empresaId, fn($q) => $q->whereHas('sede', fn($sq) => $sq->where('empresa_id', $this->empresaId)))
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

        // 1. Visitas (VisitCertificate)
        $visitas = VisitCertificate::with(['sede.empresa', 'user'])
            ->when($this->sedeId, fn($q) => $q->where('sede_id', $this->sedeId))
            ->when($this->empresaId, fn($q) => $q->whereHas('sede', fn($sq) => $sq->where('empresa_id', $this->empresaId)))
            ->when($this->tecnicoId, fn($q) => $q->where('user_id', $this->tecnicoId))
            ->latest('visit_date')
            ->limit(15)
            ->get()
            ->map(function ($visita) {
                return (object)[
                    'type' => 'visita',
                    'title' => 'Visita: ' . ($visita->status_label ?? 'General'),
                    'empresa' => $visita->sede && $visita->sede->empresa ? $visita->sede->empresa->nombre : 'N/A',
                    'user_name' => $visita->user ? ($visita->user->nombres . ' ' . $visita->user->apellidos) : 'N/A',
                    'date' => $visita->visit_date,
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
            'overdue_annual_activities' => $this->efficiency['counts']['overdue'] ?? 0,
        ];
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
                // Do nothing, let the user set the dates
                break;
            default:
                $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
                $this->dateTo = now()->endOfMonth()->format('Y-m-d');
                break;
        }
    }

    private function setGreeting()
    {
        $hour = now()->hour;
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $name = $user->nombres;

        if ($hour < 12) {
            $this->greeting = "¡Buenos días, $name!";
        } elseif ($hour < 18) {
            $this->greeting = "¡Buenas tardes, $name!";
        } else {
            $this->greeting = "¡Buenas noches, $name!";
        }
    }

    private function calculateAnnualCompliance()
    {
        // Use the same logic as render or just return the pre-calculated value
        $plans = \App\Models\AnnualPlan::where('sede_id', $this->sedeId)->where('year', $this->year)->get();
        $complianceData = $this->calculateComplianceData($plans);
        $efficiency = $this->calculateEfficiencyMetrics($complianceData);
        return $efficiency['global'];
    }
}
