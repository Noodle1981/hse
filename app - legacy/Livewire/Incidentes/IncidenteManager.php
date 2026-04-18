<?php

namespace App\Livewire\Incidentes;

use App\Models\Incidente;
use App\Models\Hallazgo;
use App\Models\Sede;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class IncidenteManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterTipo = '';
    public string $filterGravedad = '';
    public string $filterSede = '';

    // Formulario de nuevo incidente
    public $showForm = false;
    public $hallazgo_id = '';
    public $sede_id = '';
    public $tipo = 'incidente';
    public $gravedad = 'leve';
    public $fecha_ocurrencia = '';
    public $descripcion = '';
    public $personas_afectadas = 0;
    public $dias_perdidos = 0;
    public $medidas_tomadas = '';

    protected $rules = [
        'sede_id' => 'required|exists:sedes,id',
        'tipo' => 'required|in:accidente,incidente,cuasi_accidente',
        'gravedad' => 'required|in:leve,moderado,grave,fatal',
        'fecha_ocurrencia' => 'required|date',
        'descripcion' => 'required|string',
        'personas_afectadas' => 'required|integer|min:0',
        'dias_perdidos' => 'nullable|integer|min:0',
        'medidas_tomadas' => 'required|string',
    ];

    public function mount()
    {
        $this->fecha_ocurrencia = now()->format('Y-m-d');
    }

    public function save()
    {
        $this->validate();

        Incidente::create([
            'hallazgo_id' => $this->hallazgo_id ?: null,
            'sede_id' => $this->sede_id,
            'user_id' => Auth::id(),
            'tipo' => $this->tipo,
            'gravedad' => $this->gravedad,
            'fecha_ocurrencia' => $this->fecha_ocurrencia,
            'descripcion' => $this->descripcion,
            'personas_afectadas' => $this->personas_afectadas,
            'dias_perdidos' => $this->dias_perdidos,
            'medidas_tomadas' => $this->medidas_tomadas,
        ]);

        $this->reset(['showForm', 'hallazgo_id', 'sede_id', 'descripcion', 'personas_afectadas', 'dias_perdidos', 'medidas_tomadas']);
        $this->fecha_ocurrencia = now()->format('Y-m-d');

        session()->flash('message', 'Incidente registrado correctamente.');
    }

    public function delete($id)
    {
        Incidente::findOrFail($id)->delete();
        session()->flash('message', 'Incidente eliminado correctamente.');
    }

    public function render()
    {
        $query = Incidente::query()
            ->with(['sede', 'user', 'hallazgo'])
            ->latest('fecha_ocurrencia');

        if ($this->search) {
            $query->where('descripcion', 'like', '%' . $this->search . '%');
        }

        if ($this->filterTipo) {
            $query->where('tipo', $this->filterTipo);
        }

        if ($this->filterGravedad) {
            $query->where('gravedad', $this->filterGravedad);
        }

        if ($this->filterSede) {
            $query->where('sede_id', $this->filterSede);
        }

        $incidentes = $query->paginate(15);

        // KPIs
        $kpis = [
            'total' => Incidente::count(),
            'accidentes' => Incidente::accidentes()->count(),
            'graves' => Incidente::graves()->count(),
            'dias_perdidos' => Incidente::sum('dias_perdidos'),
            'personas_afectadas' => Incidente::sum('personas_afectadas'),
        ];

        $sedes = Sede::orderBy('nombre_sede')->get();
        $hallazgos = Hallazgo::abiertos()->orderBy('titulo')->get();

        return view('livewire.incidentes.incidente-manager', [
            'incidentes' => $incidentes,
            'kpis' => $kpis,
            'sedes' => $sedes,
            'hallazgos' => $hallazgos,
        ]);
    }
}
