<?php

namespace App\Livewire\Technician;

use App\Models\ChecklistAssignment;
use App\Models\Empresa;
use App\Models\Sede;
use Livewire\Component;
use Livewire\WithPagination;

class MyAssignments extends Component
{
    use WithPagination;

    public $selectedEmpresa = null;
    public $selectedSede = null;
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedEmpresa()
    {
        $this->selectedSede = null;
        $this->resetPage();
    }

    public function updatingSelectedSede()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        $assignments = ChecklistAssignment::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'in_progress', 'completed'])
            ->with(['checklist', 'empresa', 'sede'])
            ->when($this->selectedEmpresa, fn($q) => $q->where('empresa_id', $this->selectedEmpresa))
            ->when($this->selectedSede, fn($q) => $q->where('sede_id', $this->selectedSede))
            ->when($this->search, function($q) {
                $q->whereHas('checklist', fn($sq) => $sq->where('title', 'like', '%' . $this->search . '%'))
                  ->orWhereHas('empresa', fn($sq) => $sq->where('nombre', 'like', '%' . $this->search . '%'))
                  ->orWhereHas('sede', fn($sq) => $sq->where('nombre_sede', 'like', '%' . $this->search . '%'));
            })
            ->orderByRaw("CASE 
                WHEN status = 'pending' THEN 1 
                WHEN status = 'in_progress' THEN 2 
                WHEN status = 'completed' THEN 3 
                ELSE 4 
            END")
            ->orderBy('due_date', 'asc')
            ->paginate(12);

        $empresas = Empresa::whereHas('assignments', fn($q) => $q->where('user_id', $user->id))->get();
        $sedes = $this->selectedEmpresa 
            ? Sede::where('empresa_id', $this->selectedEmpresa)
                  ->whereHas('assignments', fn($q) => $q->where('user_id', $user->id))
                  ->get()
            : collect();

        // Trainings assignments
        $trainings = \App\Models\Training::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['scheduled', 'completed'])
            ->when($this->selectedEmpresa, fn($q) => $q->where('empresa_id', $this->selectedEmpresa))
            ->when($this->selectedSede, fn($q) => $q->where('sede_id', $this->selectedSede))
            ->when($this->search, function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhereHas('empresa', fn($sq) => $sq->where('nombre', 'like', '%' . $this->search . '%'))
                  ->orWhereHas('sede', fn($sq) => $sq->where('nombre_sede', 'like', '%' . $this->search . '%'));
            })
            ->orderBy('date', 'asc')
            ->get(); // Generalmente son pocas, usamos get() en lugar de paginate para simplificar UI mixta

        // EPP Delivery Requests (Solicitudes de Admin)
        $eppRequests = \App\Models\EppDeliveryRequest::query()
            ->where('status', 'pending')
            ->when($this->selectedEmpresa, fn($q) => $q->where('empresa_id', $this->selectedEmpresa))
            ->when($this->selectedSede, fn($q) => $q->where('sede_id', $this->selectedSede))
            ->with(['empresa', 'sede', 'employee', 'requester'])
            ->latest()
            ->get();

        return view('livewire.technician.my-assignments', [
            'assignments' => $assignments,
            'trainings' => $trainings,
            'eppRequests' => $eppRequests,
            'empresas' => $empresas,
            'sedes' => $sedes,
        ])->layout('components.layouts.app');
    }
}
