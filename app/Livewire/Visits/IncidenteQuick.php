<?php

namespace App\Livewire\Visits;

use App\Models\Incidente;
use App\Models\IncidenteFoto;
use App\Models\VisitCertificate;
use Livewire\Component;
use Livewire\WithFileUploads;

class IncidenteQuick extends Component
{
    use WithFileUploads;

    public $visit;
    public $titulo = '';
    public $descripcion = '';
    public $ubicacion_especifica = '';
    public $gravedad = 'leve';
    public $tipo_incidente = 'accidente';
    public $photos = [];

    protected $rules = [
        'titulo' => 'required|string|max:255',
        'descripcion' => 'required|string',
        'ubicacion_especifica' => 'nullable|string|max:255',
        'gravedad' => 'required|in:leve,moderado,grave,muy_grave',
        'tipo_incidente' => 'required|in:accidente,casi_accidente,condicion_insegura,acto_inseguro',
        'photos.*' => 'image|max:5120',
    ];

    protected $messages = [
        'titulo.required' => 'El título es obligatorio',
        'descripcion.required' => 'La descripción es obligatoria',
        'gravedad.required' => 'La gravedad es obligatoria',
        'tipo_incidente.required' => 'El tipo de incidente es obligatorio',
    ];

    public function mount(VisitCertificate $visit)
    {
        $this->visit = $visit;
    }

    public function save()
    {
        $this->validate();

        $incidente = Incidente::create([
            'visit_certificate_id' => $this->visit->id,
            'sede_id' => $this->visit->sede_id,
            'user_id' => auth()->id(),
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'ubicacion_especifica' => $this->ubicacion_especifica,
            'gravedad' => $this->gravedad === 'muy_grave' ? 'fatal' : $this->gravedad,
            'tipo' => $this->tipo_incidente === 'casi_accidente' ? 'cuasi_accidente' : 'accidente', // Mapeo para cumplir con enum DB
            'estado' => 'reportado',
            'fecha_ocurrencia' => now(),
        ]);

        // Guardar fotos
        foreach ($this->photos as $photo) {
            $path = $photo->store('incidentes/fotos', 'public');
            
            IncidenteFoto::create([
                'incidente_id' => $incidente->id,
                'ruta_archivo' => $path,
                'descripcion' => 'Evidencia del incidente - ' . now()->format('d/m/Y')
            ]);
        }

        $this->dispatch('incidenteCreated');
        $this->dispatch('closeIncidenteQuick');
        
        session()->flash('message', 'Incidente reportado correctamente.');
    }

    public function render()
    {
        return view('livewire.visits.incidente-quick');
    }
}
