<?php

namespace App\Livewire\Hallazgos;

use App\Models\Hallazgo;
use App\Models\Sede;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class HallazgoManager extends Component
{
    use WithPagination;

    // Filtros
    public string $search = '';
    public string $filterEstado = '';
    public string $filterCriticidad = '';
    public string $filterSede = '';
    public string $filterTecnico = '';
    public string $filterVencidos = '';
    
    // Ordenamiento
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterEstado' => ['except' => ''],
        'filterCriticidad' => ['except' => ''],
        'filterSede' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterEstado()
    {
        $this->resetPage();
    }

    public function updatingFilterCriticidad()
    {
        $this->resetPage();
    }

    public function updatingFilterSede()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function delete($id)
    {
        $hallazgo = Hallazgo::findOrFail($id);
        
        // Eliminar fotos asociadas (se eliminan automáticamente por el boot del modelo)
        $hallazgo->fotos()->delete();
        
        // Eliminar seguimientos
        $hallazgo->seguimientos()->delete();
        
        // Eliminar hallazgo
        $hallazgo->delete();
        
        session()->flash('message', 'Hallazgo eliminado correctamente.');
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filterEstado', 'filterCriticidad', 'filterSede', 'filterTecnico', 'filterVencidos']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Hallazgo::query()
            ->with(['sede', 'user', 'seguimientos'])
            ->withCount(['seguimientos', 'fotos', 'incidentes']);

        // Búsqueda
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('titulo', 'like', '%' . $this->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $this->search . '%')
                  ->orWhere('ubicacion_especifica', 'like', '%' . $this->search . '%');
            });
        }

        // Filtros
        if ($this->filterEstado) {
            $query->where('estado', $this->filterEstado);
        }

        if ($this->filterCriticidad) {
            $query->where('criticidad', $this->filterCriticidad);
        }

        if ($this->filterSede) {
            $query->where('sede_id', $this->filterSede);
        }

        if ($this->filterTecnico) {
            $query->where('user_id', $this->filterTecnico);
        }

        if ($this->filterVencidos === 'si') {
            $query->vencidos();
        } elseif ($this->filterVencidos === 'no') {
            $query->where(function ($q) {
                $q->where('plazo_correccion', '>=', now())
                  ->orWhereIn('estado', [Hallazgo::ESTADO_RESUELTO, Hallazgo::ESTADO_CERRADO]);
            });
        }

        // Ordenamiento
        $query->orderBy($this->sortField, $this->sortDirection);

        $hallazgos = $query->paginate(15);

        // KPIs
        $kpis = [
            'total' => Hallazgo::count(),
            'pendientes' => Hallazgo::pendientes()->count(),
            'criticos' => Hallazgo::criticos()->abiertos()->count(),
            'vencidos' => Hallazgo::vencidos()->count(),
            'resueltos_mes' => Hallazgo::resueltos()
                ->whereMonth('fecha_resolucion', now()->month)
                ->count(),
        ];

        // Datos para filtros
        $sedes = Sede::orderBy('nombre_sede')->get();
        $tecnicos = User::whereIn('rol', ['tecnico', 'técnico'])
            ->orderBy('nombres')
            ->get();

        return view('livewire.hallazgos.hallazgo-manager', [
            'hallazgos' => $hallazgos,
            'kpis' => $kpis,
            'sedes' => $sedes,
            'tecnicos' => $tecnicos,
        ]);
    }
}
