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
    public $sedeId;
    public $year;
    public $editMode = false;
    public $filterCategory = null;
    
    // Form fields for new activity
    public $category = 'Capacitación';
    public $frequency = 'none';
    public $activity_name;
    public $activity_code;
    public $checklist_id;

    public function mount($sede_id = null, $year = null, $category = null)
    {
        $this->year = $year ?? date('Y');
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
            $this->sedeId = Sede::where('id', '>', 0)->first()?->id; // Use where to ensure query
        }
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

    public function toggleMonth($planId, $month)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isAdmin() || !$this->editMode) return;
        
        $plan = AnnualPlan::findOrFail($planId);
        $plan->$month = !$plan->$month;
        $plan->save();
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

        $planData = [
            'sede_id' => $this->sedeId,
            'year' => $this->year,
            'category' => $this->category,
            'frequency' => $this->category === 'Informe' ? $this->frequency : 'none',
            'activity_name' => $this->activity_name,
            'activity_code' => $this->activity_code,
            'checklist_id' => $this->category === 'Inspección' ? $this->checklist_id : null,
        ];

        // Autocompletar meses si es Informe con frecuencia
        if ($this->category === 'Informe' && $this->frequency !== 'none') {
            $months = [
                'january', 'february', 'march', 'april', 'may', 'june',
                'july', 'august', 'september', 'october', 'november', 'december'
            ];
            
            foreach ($months as $index => $m) {
                $monthNum = $index + 1;
                $shouldCheck = false;
                
                if ($this->frequency === 'mensual') {
                    $shouldCheck = true;
                } elseif ($this->frequency === 'bimestral' && $monthNum % 2 === 0) {
                    $shouldCheck = true; // Feb, Abr, Jun, Ago, Oct, Dic
                } elseif ($this->frequency === 'trimestral' && $monthNum % 3 === 0) {
                    $shouldCheck = true; // Mar, Jun, Sep, Dic
                }
                
                $planData[$m] = $shouldCheck;
            }
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
            
        $url = route($routeName, ['sede_id' => $this->sedeId, 'year' => $this->year]);
        $this->dispatch('open-url', url: $url);
    }

    /**
     * Precalcular datos de cumplimiento con detalles específicos por categoría
     */
    private function calculateComplianceData($plans)
    {
        $data = [];
        
        // 1. Inspecciones con Títulos, Resultados y Hallazgos
        $inspections = \App\Models\DynamicInspection::where('sede_id', $this->sedeId)
            ->whereYear('completed_at', $this->year)
            ->where('status', 'completed')
            ->with(['checklist:id,title'])
            ->withCount(['hallazgos' => function($q) {
                $q->where('estado', '!=', 'cerrado');
            }])
            ->get()
            ->groupBy(function($i) {
                return $i->dynamic_checklist_id . '_' . $i->completed_at->month;
            });
            
        // 2. Capacitaciones Estándar
        $trainings = \App\Models\Training::where('sede_id', $this->sedeId)
            ->whereYear('date', $this->year)
            ->withCount('attendances')
            ->get()
            ->groupBy(function($t) {
                return \Carbon\Carbon::parse($t->date ?? $t->created_at)->month;
            });

        // 3. Capacitaciones Ad-hoc de Visitas (Auto-asignadas)
        $visitTrainings = \App\Models\VisitTraining::whereHas('visit', function($q) {
                $q->where('sede_id', $this->sedeId)
                  ->whereYear('fecha_visita', $this->year);
            })
            ->get()
            ->groupBy(function($vt) {
                return $vt->created_at->month;
            });

        // 4. Informes Maestros
        $reports = \App\Models\MasterReport::where('sede_id', $this->sedeId)
            ->whereYear('created_at', $this->year)
            ->get()
            ->groupBy(function($r) {
                $period = $r->periodo;
                if (str_contains($period, '-Q')) {
                    $q = (int)substr($period, -1);
                    return ($q - 1) * 3 + 1; // Q1=1, Q2=4, Q3=7, Q4=10
                }
                try {
                    return \Carbon\Carbon::parse($period . '-01')->month;
                } catch (\Exception $e) {
                    return $r->created_at->month;
                }
            });

        // 5. Incidentes Mensuales de la Sede
        $incidents = \App\Models\Incidente::where('sede_id', $this->sedeId)
            ->whereYear('fecha_ocurrencia', $this->year)
            ->get()
            ->groupBy(function($inc) {
                return $inc->fecha_ocurrencia->month;
            });
        
        $currentMonth = (int)date('m');
        $months = [
            'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4,
            'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8,
            'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12
        ];
        
        foreach ($plans as $plan) {
            $data[$plan->id] = [];
            
            foreach ($months as $monthName => $monthNumber) {
                $items = [];
                $count = 0;
                $findings = 0;
                $passed = 0;
                $failed = 0;
                $attendees = 0;

                if (!$plan->$monthName) {
                    $data[$plan->id][$monthName] = [
                        'status' => 'none', 
                        'count' => 0, 
                        'details' => ['items' => []]
                    ];
                    continue;
                }

                if ($plan->category === 'Inspección' && $plan->checklist_id) {
                    $key = $plan->checklist_id . '_' . $monthNumber;
                    $monthInps = $inspections[$key] ?? collect();
                    
                    foreach ($monthInps as $inp) {
                        $count++;
                        if ($inp->result === 'Pass') $passed++;
                        else $failed++;
                        $findings += $inp->hallazgos_count;
                    }
                } elseif ($plan->category === 'Capacitación') {
                    // Solo capacitaciones que coincidan con el nombre de la actividad pactada
                    $monthCaps = $trainings[$monthNumber] ?? collect();
                    $matchingCaps = $monthCaps->filter(fn($c) => $c->title === $plan->activity_name);
                    foreach ($matchingCaps as $cap) {
                        $attendees += $cap->attendances_count;
                        $count++;
                    }
                    
                    // Capacitaciones de Visita (Ad-hoc) que coincidan con el nombre
                    $monthVisitCaps = $visitTrainings[$monthNumber] ?? collect();
                    $matchingVisitCaps = $monthVisitCaps->filter(fn($vc) => $vc->title === $plan->activity_name);
                    foreach ($matchingVisitCaps as $vcap) {
                        $attendees += $vcap->attendees_count;
                        $count++;
                    }
                } elseif ($plan->category === 'Informe') {
                    $monthReports = $reports[$monthNumber] ?? collect();
                    $count = $monthReports->count();
                }
                
                $details = [
                    'passed' => $passed,
                    'failed' => $failed,
                    'findings' => $findings,
                    'attendees' => $attendees,
                    'type' => strtolower($plan->category)
                ];

                if ($count > 0) {
                    $data[$plan->id][$monthName] = ['status' => 'completed', 'count' => $count, 'details' => $details];
                } elseif ($monthNumber < $currentMonth) {
                    $data[$plan->id][$monthName] = ['status' => 'overdue', 'count' => 0, 'details' => $details];
                } else {
                    $data[$plan->id][$monthName] = ['status' => 'pending', 'count' => 0, 'details' => $details];
                }
            }
        }
        
        return $data;
    }

    public function render()
    {
        $plans = AnnualPlan::where('sede_id', $this->sedeId)
            ->where('year', $this->year)
            ->when($this->filterCategory, fn($q) => $q->where('category', $this->filterCategory))
            ->orderBy('category')
            ->get();

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $sedes = $user->isAdmin()
            ? Sede::with('empresa')->orderBy('nombre_sede')->get() 
            : $user->sedes()->with('empresa')->orderBy('nombre_sede')->get();

        $activeSede = Sede::with('empresa')->find($this->sedeId);
        
        // Precalcular datos de cumplimiento
        $complianceData = $this->calculateComplianceData($plans);
        $efficiency = $this->calculateEfficiencyMetrics($complianceData);

        return view('livewire.contracts.annual-contract-dashboard', [
            'plans' => $plans,
            'sedes' => $sedes,
            'activeSede' => $activeSede,
            'checklists' => DynamicChecklist::all(),
            'complianceData' => $complianceData,
            'efficiency' => $efficiency,
            'months' => [
                'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4,
                'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8,
                'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12
            ]
        ]);
    }

    private function calculateEfficiencyMetrics($complianceData)
    {
        $stats = [
            'global' => 0,
            'inspeccion' => 0,
            'capacitacion' => 0,
            'informe' => 0,
            'counts' => [
                'total' => 0, 'completed' => 0, 'overdue' => 0, 'pending' => 0,
                'insp_total' => 0, 'insp_done' => 0,
                'cap_total' => 0, 'cap_done' => 0,
                'inf_total' => 0, 'inf_done' => 0,
            ]
        ];

        foreach ($complianceData as $planId => $months) {
            $plan = AnnualPlan::find($planId);
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
