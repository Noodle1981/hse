<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\Empresa;
use App\Models\Sede;
use Livewire\Component;
use Livewire\WithPagination;

class TechnicianManager extends Component
{
    use WithPagination;

    // Search and Filtering
    public string $search = '';
    
    // Modal Interaction
    public bool $showAssignModal = false;
    public ?User $selectedTechnician = null;
    
    // Assignment Form
    public $selectedCompanyId = '';
    public $selectedSedeId = '';
    
    // Data Sources for Modal
    public $companies = [];
    public $sedes = [];
    public $currentAssignments = [];

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        // Initial load of companies
        $this->companies = Empresa::orderBy('nombre')->get();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedCompanyId($value)
    {
        $this->selectedSedeId = '';
        if ($value) {
            $this->sedes = Sede::where('empresa_id', $value)->orderBy('nombre_sede')->get();
        } else {
            $this->sedes = [];
        }
    }

    public function openAssignModal($userId)
    {
        $this->selectedTechnician = User::find($userId);
        $this->resetForm();
        $this->loadAssignments();
        $this->showAssignModal = true;
    }

    public function closeAssignModal()
    {
        $this->showAssignModal = false;
        $this->selectedTechnician = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->selectedCompanyId = '';
        $this->selectedSedeId = '';
        $this->sedes = [];
    }

    public function loadAssignments()
    {
        if ($this->selectedTechnician) {
            $this->currentAssignments = $this->selectedTechnician->sedes()
                ->with('empresa')
                ->get()
                ->sortBy(function($sede) {
                    return $sede->empresa->nombre . ' - ' . $sede->nombre_sede;
                });
        }
    }

    public function assignSede()
    {
        $this->validate([
            'selectedCompanyId' => 'required|exists:empresas,id',
            'selectedSedeId' => 'required|exists:sedes,id',
        ]);

        if (!$this->selectedTechnician) {
            return;
        }

        // Check availability
        if ($this->selectedTechnician->sedes()->where('sedes.id', $this->selectedSedeId)->exists()) {
            session()->flash('error', 'El técnico ya está asignado a esta sede.');
            return;
        }

        $this->selectedTechnician->sedes()->attach($this->selectedSedeId);
        
        session()->flash('message', 'Sede asignada correctamente.');
        $this->loadAssignments();
        $this->resetForm();
    }

    public function removeAssignment($sedeId)
    {
        if (!$this->selectedTechnician) return;

        $this->selectedTechnician->sedes()->detach($sedeId);
        
        session()->flash('message', 'Asignación eliminada correctamente.');
        $this->loadAssignments();
    }

    public function render()
    {
        $query = User::query()
            ->where('rol', User::ROLE_TECNICO);
            // If soft delete is enabled (which it is), those are excluded by default thanks to SoftDeletes trait.

        if ($this->search) {
            $query->where(function($q) {
                $q->where('nombres', 'like', '%' . $this->search . '%')
                  ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('n_doc', 'like', '%' . $this->search . '%');
            });
        }

        $technicians = $query->orderBy('nombres')->paginate(10);

        return view('livewire.users.technician-manager', [
            'technicians' => $technicians
        ])->layout('components.layouts.app', ['title' => 'Gestión de Técnicos']);
    }
}
