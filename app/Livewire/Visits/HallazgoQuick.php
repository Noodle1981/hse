<?php

namespace App\Livewire\Visits;

use App\Models\Hallazgo;
use App\Models\HallazgoFoto;
use App\Models\VisitCertificate;
use Livewire\Component;
use Livewire\WithFileUploads;

class HallazgoQuick extends Component
{
    use WithFileUploads;

    public \App\Models\VisitCertificate $visit;
    public $titulo = '';
    public $descripcion = '';
    public $ubicacion_especifica = '';
    public $criticidad = 'medio';
    public $plazo_correccion;
    public $photos = [];

    protected $rules = [
        'visit' => 'nullable',
        'titulo' => 'required|string|max:255',
        'descripcion' => 'required|string',
        'ubicacion_especifica' => 'nullable|string|max:255',
        'criticidad' => 'required|in:bajo,medio,alto,critico',
        'plazo_correccion' => 'required|date|after:today',
        'photos.*' => 'image|max:20480', // Aumentado a 20MB para fotos de alta resolución
    ];

    protected $messages = [
        'titulo.required' => 'El título es obligatorio',
        'descripcion.required' => 'La descripción es obligatoria',
        'plazo_correccion.required' => 'El plazo de corrección es obligatorio',
        'plazo_correccion.after' => 'El plazo debe ser posterior a hoy',
    ];

    public function mount(VisitCertificate $visit)
    {
        $this->visit = $visit;
        $this->plazo_correccion = now()->addDays(15)->format('Y-m-d');
    }

    public function save()
    {
        $this->validate();

        $hallazgo = Hallazgo::create([
            'visit_certificate_id' => $this->visit->id,
            'sede_id' => $this->visit->sede_id,
            'user_id' => auth()->id(),
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'ubicacion_especifica' => $this->ubicacion_especifica,
            'criticidad' => $this->criticidad,
            'estado' => 'pendiente',
            'plazo_correccion' => $this->plazo_correccion,
            'fecha_deteccion' => now(),
        ]);

        // Guardar fotos
        foreach ($this->photos as $photo) {
            $path = $photo->store('hallazgos/fotos', 'public');
            
            HallazgoFoto::create([
                'hallazgo_id' => $hallazgo->id,
                'tipo' => HallazgoFoto::TIPO_ANTES,
                'ruta_archivo' => $path,
                'descripcion' => 'Evidencia detectada durante la visita - ' . now()->format('d/m/Y')
            ]);
        }

        $this->dispatch('hallazgoCreated');
        $this->dispatch('closeHallazgoQuick');
        
        session()->flash('message', 'Hallazgo registrado correctamente.');
    }

    public function render()
    {
        return view('livewire.visits.hallazgo-quick');
    }
}
