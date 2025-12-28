<?php

namespace App\Livewire\Trainings;

use Livewire\Component;

use App\Models\Training;
use App\Models\Empresa;
use App\Models\Sede;
use Livewire\WithPagination;

class TrainingIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $empresaFilter = '';
    public $sedeFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'empresaFilter' => ['as' => 'empresa', 'except' => ''],
        'sedeFilter' => ['as' => 'sede', 'except' => ''],
    ];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingEmpresaFilter() { $this->resetPage(); $this->sedeFilter = ''; }
    public function updatingSedeFilter() { $this->resetPage(); }

    public function delete($id)
    {
        // Validar que solo Admin puede eliminar
        if (!auth()->user()->isAdmin()) {
            session()->flash('error', 'No tienes permisos para eliminar capacitaciones.');
            return;
        }

        $training = Training::findOrFail($id);
        
        // Validar que no tenga asistencias registradas
        if ($training->attendances()->count() > 0) {
            session()->flash('error', 'No se puede eliminar una capacitación con asistencias registradas.');
            return;
        }
        
        $training->delete();
        session()->flash('message', 'Capacitación eliminada correctamente.');
    }

    public function render()
    {
        $trainings = Training::query()
            ->with(['empresa', 'sede'])
            ->withCount('attendances')
            ->when($this->search, fn($q) => $q->where('title', 'like', '%' . $this->search . '%'))
            ->when($this->empresaFilter, fn($q) => $q->where('empresa_id', $this->empresaFilter))
            ->when($this->sedeFilter, fn($q) => $q->where('sede_id', $this->sedeFilter))
            ->latest()
            ->paginate(10);

        $empresas = Empresa::orderBy('nombre')->get();
        $sedes = $this->empresaFilter 
            ? Sede::where('empresa_id', $this->empresaFilter)->orderBy('nombre_sede')->get() 
            : collect();

        return view('livewire.trainings.training-index', [
            'trainings' => $trainings,
            'empresas' => $empresas,
            'sedes' => $sedes,
        ])->layout('components.layouts.app');
    }
}
