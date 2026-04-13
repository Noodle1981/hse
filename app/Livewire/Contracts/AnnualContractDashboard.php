<?php

namespace App\Livewire\Contracts;

use App\Models\AnnualPlan;
use App\Models\Sede;
use App\Models\DynamicChecklist;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class AnnualContractDashboard extends Component
{
    public function getActiveVisit()
    {
        return \App\Models\VisitCertificate::where('user_id', Auth::id())
            ->where('status', 'in_progress')
            ->first();
    }

    public function redirectToTask($visitId, $activityName, $checklistId = null, $category = 'Inspección')
    {
        $taskId = $checklistId ?: 'name:' . $activityName;
        return redirect()->route('tecnico.visits.in-progress', [
            'visit' => $visitId,
            'task_id' => $taskId,
            'type' => $category,
            'source' => 'contract'
        ]);
    }


    public $sedeId;
    public $selectedCompanyId;
    public $contractStart; // e.g., '2025-11'
    public $contractDuration = 12; // in months
    public $editMode = false;
    public $editingCell = null;  // "{planId}_{monthValue}"
    public $editingDate  = '';   // fecha específica siendo editada
    public $filterCategory = null;

    // Form fields for new activity
    public $category = 'Capacitación';
    public $frequency = 'none';
    public $activity_name;
    public $activity_code;
    public $checklist_id;

    public function mount($sede_id = null, $category = null)
    {
        $this->contractStart = date('Y-m');
        $this->filterCategory = $category;
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Default to first available sede
        $role = strtolower($user->rol);
        if ($sede_id) {
            $this->sedeId = $sede_id;
        } elseif ($role === 'técnico' || $role === 'tecnico') {
            $this->sedeId = $user->sedes()->first()?->id;
        } else {
            $this->sedeId = Sede::where('id', '>', 0)->first()?->id;
        }

        if ($this->sedeId) {
            $this->selectedCompanyId = Sede::find($this->sedeId)?->empresa_id;
            $this->detectContractSettings();
        }
    }

    /**
     * Detecta la duración e inicio del contrato basándose en planes existentes
     */
    private function detectContractSettings()
    {
        if (!$this->sedeId) return;

        $latestPlan = AnnualPlan::where('sede_id', $this->sedeId)
            ->latest('id')
            ->first();

        if ($latestPlan && $latestPlan->period_start && $latestPlan->period_end) {
            $this->contractStart = $latestPlan->period_start->format('Y-m');

            // Calcular diferencia en meses
            $months = (int) $latestPlan->period_start->diffInMonths($latestPlan->period_end) + 1;

            // Solo asignar si es una opción válida en el select (3, 6, 12, 24)
            if (in_array($months, [3, 6, 12, 24])) {
                $this->contractDuration = $months;
            } else {
                // Fallback inteligente si no es exacto
                $this->contractDuration = $months > 12 ? 24 : ($months > 6 ? 12 : ($months > 3 ? 6 : 3));
            }
        }
    }

    public function updatedSelectedCompanyId($value)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isAdmin()) {
            $this->sedeId = Sede::where('empresa_id', $value)->first()?->id;
        } else {
            $this->sedeId = $user->sedes()->where('empresa_id', $value)->first()?->id;
        }
        $this->detectContractSettings();
    }

    public function updatedSedeId($value)
    {
        $this->detectContractSettings();
    }

    public function updatedCategory($value)
    {
        if ($value === 'Informe') {
            $this->activity_name = 'Informe HSE';
            $this->activity_code = 'EP-INF-01';
            $this->checklist_id = null;
        } else {
            $this->activity_name = '';
            $this->activity_code = '';
        }
    }

    public function updatedChecklistId($value)
    {
        if (!$value) {
            $this->activity_name = '';
            $this->activity_code = '';
            return;
        }

        $checklist = DynamicChecklist::find($value);
        if ($checklist) {
            $this->activity_name = $checklist->title;
            $this->activity_code = $checklist->registry_code ?? '';
        }
    }

    public function toggleMonth($planId, $monthValue)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isAdmin() || !$this->editMode) return;

        $plan = AnnualPlan::findOrFail($planId);
        $scheduled = $plan->scheduled_months ?? [];

        if (in_array($monthValue, $scheduled)) {
            $scheduled = array_values(array_diff($scheduled, [$monthValue]));
            // Remove specific date when deactivating month
            $dates = $plan->scheduled_dates ?? [];
            unset($dates[$monthValue]);
            $plan->scheduled_dates = $dates;
        } else {
            $scheduled[] = $monthValue;
            sort($scheduled);
        }

        $plan->scheduled_months = $scheduled;
        $plan->save();
    }

    public function openDatePicker($planId, $monthValue)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isAdmin() || !$this->editMode) return;

        $plan = AnnualPlan::findOrFail($planId);
        if (!in_array($monthValue, $plan->scheduled_months ?? [])) return;

        $this->editingCell = "{$planId}_{$monthValue}";
        $dates = $plan->scheduled_dates ?? [];
        $this->editingDate = $dates[$monthValue] ?? '';
    }

    public function saveScheduledDate()
    {
        if (!$this->editingCell) return;

        [$planId, $monthValue] = explode('_', $this->editingCell, 2);
        $plan = AnnualPlan::findOrFail($planId);

        $dates = $plan->scheduled_dates ?? [];

        if ($this->editingDate) {
            $date = \Carbon\Carbon::parse($this->editingDate);
            $monthStart = \Carbon\Carbon::createFromFormat('Y-m', $monthValue)->startOfMonth();
            $monthEnd   = $monthStart->copy()->endOfMonth();

            if ($date->lt($monthStart) || $date->gt($monthEnd)) {
                $this->addError('editingDate', 'La fecha debe pertenecer al mes seleccionado.');
                return;
            }
            $dates[$monthValue] = $this->editingDate;
        } else {
            unset($dates[$monthValue]); // Sin fecha = vuelve al fin de mes
        }

        $plan->scheduled_dates = $dates;
        $plan->save();

        $this->editingCell = null;
        $this->editingDate = '';
    }

    public function addActivity()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isAdmin()) return;

        $this->validate([
            'activity_name' => 'required|string|max:255',
            'category' => 'required|in:Capacitación,Inspección,Informe',
            'frequency' => 'required|in:none,mensual,bimestral,trimestral',
            'sedeId' => 'required|exists:sedes,id',
        ]);

        $startDate = \Carbon\Carbon::createFromFormat('Y-m', $this->contractStart)->startOfMonth();
        $endDate = $startDate->copy()->addMonths($this->contractDuration - 1)->endOfMonth();

        $planData = [
            'sede_id' => $this->sedeId,
            'period_start' => $startDate,
            'period_end' => $endDate,
            'category' => $this->category,
            'frequency' => $this->category === 'Informe' ? $this->frequency : 'none',
            'activity_name' => $this->activity_name,
            'activity_code' => $this->activity_code,
            'checklist_id' => $this->category === 'Inspección' ? $this->checklist_id : null,
            'scheduled_months' => []
        ];

        // Autocompletar meses si es Informe u otra categoría con frecuencia (actualmente solo Informe usa frequency en UI inicial)
        if ($this->frequency !== 'none') {
            $scheduled = [];
            for ($i = 0; $i < $this->contractDuration; $i++) {
                $monthNum = $i + 1;
                $shouldCheck = false;

                if ($this->frequency === 'mensual') {
                    $shouldCheck = true;
                } elseif ($this->frequency === 'bimestral' && $monthNum % 2 === 0) {
                    $shouldCheck = true;
                } elseif ($this->frequency === 'trimestral' && $monthNum % 3 === 0) {
                    $shouldCheck = true;
                }

                if ($shouldCheck) {
                    $mDate = $startDate->copy()->addMonths($i);
                    $scheduled[] = $mDate->format('Y-m');
                }
            }
            $planData['scheduled_months'] = $scheduled;
        }

        AnnualPlan::create($planData);

        $this->reset(['activity_name', 'activity_code', 'checklist_id', 'frequency']);
        $this->category = 'Capacitación';
    }

    public function deleteActivity($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isAdmin()) return;

        AnnualPlan::findOrFail($id)->delete();
    }

    public function downloadPdf()
    {
        if (!$this->sedeId) return;

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $routeName = $user->isAdmin()
            ? 'gestor.annual-contract.pdf'
            : 'tecnico.annual-contract.pdf';

        // Pass period_start implicitly OR let them download the whole year? No, we pass the current start
        $url = route($routeName, ['sede_id' => $this->sedeId, 'year' => substr($this->contractStart, 0, 4)]);
        $this->dispatch('open-url', url: $url);
    }

    /**
     * Gets the dynamically generated months array for the grid
     */
    public function getDynamicMonthsProperty()
    {
        $months = [];
        $start = \Carbon\Carbon::createFromFormat('Y-m', $this->contractStart)->startOfMonth();

        for ($i = 0; $i < $this->contractDuration; $i++) {
            $current = $start->copy()->addMonths($i);
            $months[$current->format('Y-m')] = [
                'name' => $current->translatedFormat('F'),
                'short' => $current->translatedFormat('M'),
                'year' => $current->format('Y'),
                'month' => $current->month,
            ];
        }
        return $months;
    }

    /**
     * Precalcular datos de cumplimiento con detalles específicos por categoría
     */
    private function calculateComplianceData($plans, $dynamicMonths)
    {
        $data = [];

        if (!$this->sedeId) return [];

        $startDate = \Carbon\Carbon::createFromFormat('Y-m', $this->contractStart)->startOfMonth();
        $endDate = $startDate->copy()->addMonths($this->contractDuration - 1)->endOfMonth();

        // 1. Inspecciones con Títulos, Resultados y Hallazgos (Optimizado select)
        $inspections = \App\Models\DynamicInspection::select('id', 'dynamic_checklist_id', 'sede_id', 'completed_at', 'result', 'user_id')
            ->where('sede_id', $this->sedeId)
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->with(['checklist:id,title', 'assignment:id,inspection_id', 'assignment.visitCompletion:id,checklist_assignment_id,visit_id', 'assignment.visitCompletion.visit:id,start_time,end_time'])
            ->withCount(['hallazgos' => function ($q) {
                $q->where('estado', '!=', 'cerrado');
            }])
            ->get()
            ->groupBy(function ($i) {
                return $i->dynamic_checklist_id . '_' . $i->completed_at->format('Y-m');
            });

        // 2. Capacitaciones Estándar (Optimizado select)
        $trainings = \App\Models\Training::select('id', 'sede_id', 'date', 'title', 'duration', 'status')
            ->where('sede_id', $this->sedeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->with(['visitTrainings:id,training_id,visit_id,duration_minutes'])
            ->withCount('attendances')
            ->get()
            ->groupBy(function ($t) {
                return \Carbon\Carbon::parse($t->date)->format('Y-m');
            });

        // 3. Capacitaciones Ad-hoc de Visitas (Auto-asignadas)
        $visitTrainings = \App\Models\VisitTraining::select('id', 'visit_id', 'title', 'attendees_count', 'duration_minutes', 'created_at')
            ->whereHas('visit', function ($q) use ($startDate, $endDate) {
                $q->where('sede_id', $this->sedeId)
                    ->whereBetween('fecha_visita', [$startDate, $endDate]);
            })
            ->get()
            ->groupBy(function ($vt) {
                return $vt->created_at->format('Y-m');
            });

        // 4. Informes Maestros
        $reports = \App\Models\MasterReport::select('id', 'sede_id', 'periodo', 'created_at', 'estado')
            ->where('sede_id', $this->sedeId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($r) {
                try {
                    return \Carbon\Carbon::parse($r->periodo . '-01')->format('Y-m');
                } catch (\Exception $e) {
                    return $r->created_at->format('Y-m');
                }
            });

        // 5. Incidentes Mensuales de la Sede
        $incidents = \App\Models\Incidente::select('id', 'sede_id', 'fecha_ocurrencia')
            ->where('sede_id', $this->sedeId)
            ->whereBetween('fecha_ocurrencia', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($inc) {
                return $inc->fecha_ocurrencia->format('Y-m');
            });

        $currentDate = now()->format('Y-m');

        foreach ($plans as $plan) {
            $data[$plan->id] = [];
            $scheduledMonths = is_array($plan->scheduled_months) ? $plan->scheduled_months : json_decode($plan->scheduled_months ?? '[]', true) ?? [];

            foreach ($dynamicMonths as $monthValue => $monthInfo) {
                $items = [];
                $count = 0;
                $findings = 0;
                $passed = 0;
                $failed = 0;
                $attendees = 0;
                $executionDate = null;
                $duration = 0;

                if (!in_array($monthValue, $scheduledMonths)) {
                    $data[$plan->id][$monthValue] = [
                        'status' => 'none',
                        'count' => 0,
                        'details' => ['items' => []]
                    ];
                    continue;
                }

                if ($plan->category === 'Inspección' && $plan->checklist_id) {
                    $key = $plan->checklist_id . '_' . $monthValue;
                    $monthInps = $inspections[$key] ?? collect();

                    foreach ($monthInps as $inp) {
                        $count++;
                        if ($inp->result === 'Pass') $passed++;
                        else $failed++;
                        $findings += $inp->hallazgos_count;

                        $executionDate = $inp->completed_at?->format('d/m/Y');

                        // Intentar obtener duración de la visita
                        if ($inp->assignment?->visitCompletion?->visit) {
                            $visit = $inp->assignment->visitCompletion->visit;
                            if ($visit->start_time && $visit->end_time) {
                                $duration = (int) $visit->start_time->diffInMinutes($visit->end_time);
                            }
                        }
                    }
                } elseif ($plan->category === 'Capacitación') {
                    // Solo capacitaciones que coincidan con el nombre de la actividad pactada
                    $monthCaps = $trainings[$monthValue] ?? collect();
                    $matchingCaps = $monthCaps->filter(fn($c) => $c->title === $plan->activity_name);
                    foreach ($matchingCaps as $cap) {
                        $attendees += $cap->attendances_count;
                        $count++;
                        $executionDate = $cap->date?->format('d/m/Y');
                        $duration = (int) $cap->duration;

                        // Si no hay duración explícita, buscar en visitTrainings
                        if (!$duration && $cap->visitTrainings->isNotEmpty()) {
                            $vTrain = $cap->visitTrainings->first();
                            $duration = (int) $vTrain->duration_minutes;
                        }
                    }

                    // Capacitaciones de Visita (Ad-hoc) que coincidan con el nombre
                    $monthVisitCaps = $visitTrainings[$monthValue] ?? collect();
                    $matchingVisitCaps = $monthVisitCaps->filter(fn($vc) => $vc->title === $plan->activity_name);
                    foreach ($matchingVisitCaps as $vcap) {
                        $attendees += $vcap->attendees_count;
                        $count++;
                        $executionDate = $vcap->created_at?->format('d/m/Y');
                        $duration = (int) $vcap->duration_minutes;
                    }
                } elseif ($plan->category === 'Informe') {
                    $monthReports = $reports[$monthValue] ?? collect();
                    $count = $monthReports->count();
                    if ($count > 0) {
                        $executionDate = $monthReports->first()->created_at?->format('d/m/Y');
                    }
                }

                $details = [
                    'passed' => $passed,
                    'failed' => $failed,
                    'findings' => $findings,
                    'attendees' => $attendees,
                    'execution_date' => $executionDate,
                    'duration' => $duration,
                    'type' => strtolower($plan->category)
                ];

                if ($count > 0) {
                    $data[$plan->id][$monthValue] = ['status' => 'completed', 'count' => $count, 'details' => $details];
                } else {
                    // Use specific date if set, otherwise compare by month
                    $scheduledDates = $plan->scheduled_dates ?? [];
                    $specificDate   = $scheduledDates[$monthValue] ?? null;
                    $isOverdue = $specificDate
                        ? \Carbon\Carbon::parse($specificDate)->isPast()
                        : ($monthValue < $currentDate);

                    if ($isOverdue) {
                        $data[$plan->id][$monthValue] = ['status' => 'overdue', 'count' => 0, 'details' => $details];
                    } else {
                        $data[$plan->id][$monthValue] = ['status' => 'pending', 'count' => 0, 'details' => $details];
                    }
                }
            }
        }

        return $data;
    }

    public function render()
    {
        // Load plans where sede_id matches (and potentially we could filter by period overlapping, but if they want to manage Sede's overall list, we load all or filter by the start date roughly)
        $plans = AnnualPlan::where('sede_id', $this->sedeId)
            ->when($this->filterCategory, fn($q) => $q->where('category', $this->filterCategory))
            ->orderBy('category')
            ->get();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $availableSedes = $user->isAdmin()
            ? Sede::select('id', 'nombre_sede', 'empresa_id')->orderBy('nombre_sede')->get()
            : $user->sedes()->select('sedes.id', 'sedes.nombre_sede', 'sedes.empresa_id')->orderBy('nombre_sede')->get();

        $companies = \App\Models\Empresa::select('id', 'nombre')
            ->whereIn('id', $availableSedes->pluck('empresa_id'))
            ->orderBy('nombre')
            ->get();

        $sedes = $availableSedes->where('empresa_id', $this->selectedCompanyId);

        if (!$this->selectedCompanyId && $companies->isNotEmpty()) {
            $this->selectedCompanyId = $companies->first()->id;
        }

        $activeSede = Sede::with(['empresa', 'users' => function ($q) {
            $q->where('rol', 'tecnico');
        }])->find($this->sedeId);
        $dynamicMonths = $this->getDynamicMonthsProperty();

        // Precalcular datos de cumplimiento
        $complianceData = $this->calculateComplianceData($plans, $dynamicMonths);
        $efficiency = $this->calculateEfficiencyMetrics($complianceData, $plans);

        // Formatear opciones para custom-select
        $companyOptions = $companies->map(fn($c) => ['value' => $c->id, 'label' => $c->nombre])->toArray();
        $sedeOptions = $sedes->map(fn($s) => ['value' => $s->id, 'label' => $s->nombre_sede])->toArray();
        $checklistOptions = DynamicChecklist::select('id', 'title')->get()->map(fn($c) => ['value' => $c->id, 'label' => $c->title])->toArray();

        $durationOptions = [
            ['value' => 3, 'label' => '3 Meses'],
            ['value' => 6, 'label' => '6 Meses'],
            ['value' => 12, 'label' => 'Anual (12)'],
            ['value' => 24, 'label' => 'Bianual (24)'],
        ];

        $categoryOptions = [
            ['value' => 'Capacitación', 'label' => 'Capacitación'],
            ['value' => 'Inspección', 'label' => 'Inspección'],
            ['value' => 'Informe', 'label' => 'Informe Técnico'],
        ];

        return view('livewire.contracts.annual-contract-dashboard', [
            'plans' => $plans,
            'sedeOptions' => $sedeOptions,
            'companyOptions' => $companyOptions,
            'durationOptions' => $durationOptions,
            'categoryOptions' => $categoryOptions,
            'checklistOptions' => $checklistOptions,
            'activeSede' => $activeSede,
            'complianceData' => $complianceData,
            'efficiency' => $efficiency,
            'dynamicMonths' => $dynamicMonths
        ]);
    }

    private function calculateEfficiencyMetrics($complianceData, $plans)
    {
        $stats = [
            'global' => 0,
            'inspeccion' => 0,
            'capacitacion' => 0,
            'informe' => 0,
            'counts' => [
                'total' => 0,
                'completed' => 0,
                'overdue' => 0,
                'pending' => 0,
                'insp_total' => 0,
                'insp_done' => 0,
                'cap_total' => 0,
                'cap_done' => 0,
                'inf_total' => 0,
                'inf_done' => 0,
            ]
        ];

        foreach ($complianceData as $planId => $months) {
            $plan = $plans->firstWhere('id', $planId);
            if (!$plan) continue;
            foreach ($months as $month => $data) {
                if ($data['status'] === 'none') continue;

                $stats['counts']['total']++;
                if ($data['status'] === 'completed') $stats['counts']['completed']++;
                if ($data['status'] === 'overdue') $stats['counts']['overdue']++;
                if ($data['status'] === 'pending') $stats['counts']['pending']++;

                $cat = strtolower($plan->category);
                if ($cat === 'inspección') {
                    $stats['counts']['insp_total']++;
                    if ($data['status'] === 'completed') $stats['counts']['insp_done']++;
                } elseif ($cat === 'capacitación') {
                    $stats['counts']['cap_total']++;
                    if ($data['status'] === 'completed') $stats['counts']['cap_done']++;
                } elseif ($cat === 'informe') {
                    $stats['counts']['inf_total']++;
                    if ($data['status'] === 'completed') $stats['counts']['inf_done']++;
                }
            }
        }

        $stats['global'] = $stats['counts']['total'] > 0 ? round(($stats['counts']['completed'] / $stats['counts']['total']) * 100) : 0;
        $stats['inspeccion'] = $stats['counts']['insp_total'] > 0 ? round(($stats['counts']['insp_done'] / $stats['counts']['insp_total']) * 100) : 0;
        $stats['capacitacion'] = $stats['counts']['cap_total'] > 0 ? round(($stats['counts']['cap_done'] / $stats['counts']['cap_total']) * 100) : 0;
        $stats['informe'] = $stats['counts']['inf_total'] > 0 ? round(($stats['counts']['inf_done'] / $stats['counts']['inf_total']) * 100) : 0;

        return $stats;
    }
}
