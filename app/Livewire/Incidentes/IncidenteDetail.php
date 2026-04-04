<?php

namespace App\Livewire\Incidentes;

use App\Models\Incidente;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class IncidenteDetail extends Component
{
    public $incidenteId;
    public $incidente;

    // Formulario de Gestión (Admin)
    public $dias_perdidos = 0;
    public $medidas_tomadas = '';
    public $nuevoEstado = 'pendiente';
    
    public $showGestionForm = false;

    protected $rules = [
        'dias_perdidos' => 'nullable|integer|min:0',
        'medidas_tomadas' => 'required|string|min:5',
        'nuevoEstado' => 'required|in:pendiente,cerrado',
    ];

    public function mount($id)
    {
        $this->incidenteId = $id;
        $this->loadIncidente();
    }

    public function loadIncidente()
    {
        $this->incidente = Incidente::with([
            'sede.empresa',
            'user',
            'fotos',
            'hallazgo'
        ])->findOrFail($this->incidenteId);

        // Inicializar formulario
        $this->dias_perdidos = $this->incidente->dias_perdidos ?? 0;
        $this->medidas_tomadas = $this->incidente->medidas_tomadas ?? '';
        $this->nuevoEstado = $this->incidente->estado ?? 'pendiente';
    }

    public function actualizarIncidente()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isAdmin()) {
            return;
        }

        $this->validate();

        $this->incidente->update([
            'dias_perdidos' => $this->dias_perdidos,
            'medidas_tomadas' => $this->medidas_tomadas,
            'estado' => $this->nuevoEstado,
        ]);

        $this->showGestionForm = false;
        $this->loadIncidente();

        session()->flash('message', 'Incidente actualizado correctamente.');
    }

    public function render()
    {
        return view('livewire.incidentes.incidente-detail');
    }
}
