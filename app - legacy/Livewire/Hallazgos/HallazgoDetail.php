<?php

namespace App\Livewire\Hallazgos;

use App\Models\Hallazgo;
use App\Models\HallazgoSeguimiento;
use App\Models\HallazgoFoto;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class HallazgoDetail extends Component
{
    use WithFileUploads;

    public $hallazgoId;
    public $hallazgo;

    // Formulario de seguimiento
    public $nuevoEstado = '';
    public $comentarioSeguimiento = '';
    
    // Subida de fotos
    public $nuevaFoto = null;
    public $tipoFoto = 'durante';
    public $descripcionFoto = '';

    // Modal states
    public $showSeguimientoForm = false;
    public $showFotoForm = false;

    protected $rules = [
        'nuevoEstado' => 'required|in:pendiente,en_proceso,resuelto,verificado,cerrado',
        'comentarioSeguimiento' => 'required|string|min:10',
    ];

    protected $messages = [
        'nuevoEstado.required' => 'Debe seleccionar un estado',
        'comentarioSeguimiento.required' => 'El comentario es obligatorio',
        'comentarioSeguimiento.min' => 'El comentario debe tener al menos 10 caracteres',
    ];

    public function mount($id)
    {
        $this->hallazgoId = $id;
        $this->loadHallazgo();
    }

    public function loadHallazgo()
    {
        $this->hallazgo = Hallazgo::with([
            'sede',
            'user',
            'seguimientos.user',
            'fotos',
            'incidentes',
            'origen'
        ])->findOrFail($this->hallazgoId);

        $this->nuevoEstado = $this->hallazgo->estado;
    }

    public function agregarSeguimiento()
    {
        $this->validate();

        $this->hallazgo->agregarSeguimiento(
            $this->nuevoEstado,
            $this->comentarioSeguimiento,
            Auth::id()
        );

        $this->reset(['comentarioSeguimiento', 'showSeguimientoForm']);
        $this->loadHallazgo();

        session()->flash('message', 'Seguimiento agregado correctamente.');
    }

    public function subirFoto()
    {
        $this->validate([
            'nuevaFoto' => 'required|image|max:5120', // 5MB max
            'tipoFoto' => 'required|in:antes,durante,despues',
            'descripcionFoto' => 'nullable|string|max:255',
        ]);

        // Guardar foto
        $path = $this->nuevaFoto->store('hallazgos/fotos', 'public');

        HallazgoFoto::create([
            'hallazgo_id' => $this->hallazgoId,
            'tipo' => $this->tipoFoto,
            'ruta_archivo' => $path,
            'descripcion' => $this->descripcionFoto,
        ]);

        $this->reset(['nuevaFoto', 'tipoFoto', 'descripcionFoto', 'showFotoForm']);
        $this->loadHallazgo();

        session()->flash('message', 'Foto agregada correctamente.');
    }

    public function eliminarFoto($fotoId)
    {
        $foto = HallazgoFoto::findOrFail($fotoId);
        
        // Verificar que pertenece a este hallazgo
        if ($foto->hallazgo_id != $this->hallazgoId) {
            return;
        }

        $foto->delete(); // El archivo se elimina automáticamente por el boot del modelo

        $this->loadHallazgo();
        session()->flash('message', 'Foto eliminada correctamente.');
    }

    public function prepararSeguimiento($nuevoEstado)
    {
        $this->nuevoEstado = $nuevoEstado;
        $this->comentarioSeguimiento = '';
        $this->showSeguimientoForm = true;
    }

    public function render()
    {
        return view('livewire.hallazgos.hallazgo-detail');
    }
}
