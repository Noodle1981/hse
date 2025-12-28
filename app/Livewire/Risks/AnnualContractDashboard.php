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

    public function getComplianceData($plan, $monthName, $monthNumber)
    {
        if (!$plan->$monthName) return ['status' => 'none', 'count' => 0];

        $count = 0;
        if ($plan->category === 'Inspección' && $plan->checklist_id) {
            $count = Inspection::where('dynamic_checklist_id', $plan->checklist_id)
                ->where('sede_id', $this->sedeId)
                ->whereMonth('created_at', $monthNumber)
                ->whereYear('created_at', $this->year)
                ->count();
        }
        
        if ($count > 0) return ['status' => 'completed', 'count' => $count];
        
        // Si el mes ya pasó y no se hizo
        $currentMonth = (int)date('m');
        if ($monthNumber < $currentMonth) return ['status' => 'overdue', 'count' => 0];
        
        return ['status' => 'pending', 'count' => 0];
    }

    public function downloadPdf()
    {
        if (!$this->sedeId) return;
        $url = route('annual-contract.pdf', ['sede_id' => $this->sedeId, 'year' => $this->year]);
        $this->dispatch('open-url', url: $url);
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

        return view('livewire.risks.annual-contract-dashboard', [
            'plans' => $plans,
            'sedes' => $sedes,
            'checklists' => DynamicChecklist::all(),
            'months' => [
                'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4,
                'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8,
                'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12
            ]
        ])->layout('components.layouts.app');
    }
}
