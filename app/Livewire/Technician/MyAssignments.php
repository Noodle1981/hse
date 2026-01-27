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

    public string $search = '';
    public ?int $selectedEmpresa = null;
    public ?int $selectedSede = null;
    public string $statusFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedEmpresa' => ['except' => null],
        'selectedSede' => ['except' => null],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedEmpresa()
    {
        $this->selectedSede = null;
        $this->resetPage();
    }

    public function updatedSelectedSede()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        $query = ChecklistAssignment::query()
            ->with(['checklist', 'sede', 'empresa', 'visitCompletion.visit'])
            ->where('user_id', $user->id)
            ->when($this->search, function ($q) {
                $q->whereHas('checklist', function ($sub) {
                    $sub->where('title', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedEmpresa, function ($q) {
                $q->where('empresa_id', $this->selectedEmpresa);
            })
            ->when($this->selectedSede, function ($q) {
                $q->where('sede_id', $this->selectedSede);
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc');

        $assignments = $query->paginate(15);

        $empresas = Empresa::whereHas('checklistAssignments', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->orderBy('nombre')->get();

        $sedes = $this->selectedEmpresa
            ? Sede::where('empresa_id', $this->selectedEmpresa)
                ->whereHas('checklistAssignments', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->orderBy('nombre_sede')->get()
            : collect();

        return view('livewire.technician.my-assignments', [
            'assignments' => $assignments,
            'empresas' => $empresas,
            'sedes' => $sedes,
        ])->layout('components.layouts.app');
    }
}
