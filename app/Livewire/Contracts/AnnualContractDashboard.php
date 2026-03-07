<?php

namespace App\Livewire\Contracts;

use App\Models\AnnualPlan;
use App\Models\Sede;
use App\Models\DynamicChecklist;
use Livewire\Component;

class AnnualContractDashboard extends Component
{
    public $sedeId;
    public $year;
    public $editMode = false;
    
    // Form fields for new activity
    public $category = 'Capacitación';
    public $frequency = 'none';
    public $activity_name;
    public $activity_code;
    public $checklist_id;

    public function mount()
    {
        $this->year = date('Y');
        $user = auth()->user();
        
        // Default to first available sede
        $role = strtolower($user->rol);
        if ($role === 'técnico' || $role === 'tecnico') {
            $this->sedeId = $user->sedes()->first()?->id;
        } else {
            $this->sedeId = Sede::first()?->id;
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
        if (!auth()->user()->isAdmin() || !$this->editMode) return;
        
        $plan = AnnualPlan::findOrFail($planId);
        $plan->$month = !$plan->$month;
        $plan->save();
    }

    public function addActivity()
    {
        if (!auth()->user()->isAdmin()) return;
        
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
        if (!auth()->user()->isAdmin()) return;
        
        AnnualPlan::findOrFail($id)->delete();
    }

    public function downloadPdf()
    {
        if (!$this->sedeId) return;
        
        $routeName = auth()->user()->isAdmin() 
            ? 'gestor.annual-contract.pdf' 
            : 'tecnico.annual-contract.pdf';
            
        $url = route($routeName, ['sede_id' => $this->sedeId, 'year' => $this->year]);
        $this->dispatch('open-url', url: $url);
    }

    /**
     * Precalcular datos de cumplimiento para evitar N+1 queries
     */
    private function calculateComplianceData($plans)
    {
        $data = [];
        
        // Una sola query para todas las inspecciones del año
        $inspections = \App\Models\DynamicInspection::where('sede_id', $this->sedeId)
            ->whereYear('completed_at', $this->year)
            ->where('status', 'completed')
            ->get()
            ->groupBy(function($i) {
                return $i->dynamic_checklist_id . '_' . $i->completed_at->month;
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
                if (!$plan->$monthName) {
                    $data[$plan->id][$monthName] = ['status' => 'none', 'count' => 0];
                    continue;
                }

                $count = 0;
                if ($plan->category === 'Inspección' && $plan->checklist_id) {
                    $key = $plan->checklist_id . '_' . $monthNumber;
                    $count = isset($inspections[$key]) ? $inspections[$key]->count() : 0;
                }
                
                if ($count > 0) {
                    $data[$plan->id][$monthName] = ['status' => 'completed', 'count' => $count];
                } elseif ($monthNumber < $currentMonth) {
                    $data[$plan->id][$monthName] = ['status' => 'overdue', 'count' => 0];
                } else {
                    $data[$plan->id][$monthName] = ['status' => 'pending', 'count' => 0];
                }
            }
        }
        
        return $data;
    }

    public function render()
    {
        $plans = AnnualPlan::where('sede_id', $this->sedeId)
            ->where('year', $this->year)
            ->orderBy('category')
            ->get();

        $sedes = auth()->user()->isAdmin()
            ? Sede::all() 
            : auth()->user()->sedes;

        // Precalcular datos de cumplimiento
        $complianceData = $this->calculateComplianceData($plans);

        return view('livewire.contracts.annual-contract-dashboard', [
            'plans' => $plans,
            'sedes' => $sedes,
            'checklists' => DynamicChecklist::all(),
            'complianceData' => $complianceData,
            'months' => [
                'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4,
                'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8,
                'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12
            ]
        ])->layout('components.layouts.app');
    }
}
