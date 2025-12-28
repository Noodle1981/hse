<?php

namespace App\Livewire\Risks;

use App\Models\AnnualPlan;
use App\Models\Sede;
use App\Models\Empresa;
use App\Models\DynamicChecklist;
use App\Models\Inspection;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class AnnualContractDashboard extends Component
{
    public $sedeId;
    public $year;
    public $editMode = false;
    
    // Form fields for new activity
    public $category = 'Capacitación';
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

    public function toggleMonth($planId, $month)
    {
        $role = strtolower(auth()->user()->rol);
        if (($role !== 'administrador' && $role !== 'admin') || !$this->editMode) return;
        
        $plan = AnnualPlan::findOrFail($planId);
        $plan->$month = !$plan->$month;
        $plan->save();
    }

    public function addActivity()
    {
        $role = strtolower(auth()->user()->rol);
        if ($role !== 'administrador' && $role !== 'admin') return;
        
        $this->validate([
            'activity_name' => 'required|string|max:255',
            'category' => 'required|in:Capacitación,Inspección,Registro',
            'sedeId' => 'required|exists:sedes,id',
        ]);

        AnnualPlan::create([
            'sede_id' => $this->sedeId,
            'year' => $this->year,
            'category' => $this->category,
            'activity_name' => $this->activity_name,
            'activity_code' => $this->activity_code,
            'checklist_id' => $this->checklist_id,
        ]);

        $this->reset(['activity_name', 'activity_code', 'checklist_id']);
    }

    public function deleteActivity($id)
    {
        $role = strtolower(auth()->user()->rol);
        if ($role !== 'administrador' && $role !== 'admin') return;
        
        AnnualPlan::findOrFail($id)->delete();
    }

    public function downloadPdf()
    {
        if (!$this->sedeId) return;
        $url = route('annual-contract.pdf', ['sede_id' => $this->sedeId, 'year' => $this->year]);
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

        $role = strtolower(auth()->user()->rol);
        $sedes = ($role === 'administrador' || $role === 'admin') 
            ? Sede::all() 
            : auth()->user()->sedes;

        // Precalcular datos de cumplimiento
        $complianceData = $this->calculateComplianceData($plans);

        return view('livewire.risks.annual-contract-dashboard', [
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
