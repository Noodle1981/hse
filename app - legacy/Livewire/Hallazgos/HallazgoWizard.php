<?php

namespace App\Livewire\Hallazgos;

use App\Models\Hallazgo;
use App\Models\Sede;
use App\Models\DynamicInspection;
use App\Models\GeneradorConstancia;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class HallazgoWizard extends Component
{
    use WithFileUploads;

    public $hallazgoId = null;
    public $currentStep = 1;
    public $totalSteps = 3; // 3 steps standalone (Info, Class, Origen)

    // Paso 1: Información Básica
    public $titulo = '';
    public $descripcion = '';
    public $ubicacion_especifica = '';
    public $sede_id = '';

    // Paso 2: Clasificación
    public $criticidad = 'medio';
    public $estado = 'pendiente';
    public $plazo_correccion = '';
    public $responsable_correccion = '';

    // Paso 3: Origen (opcional)
    public $origen_type = '';
    public $origen_id = '';

    // Fotos (se manejan en el detalle después de crear)
    public $fotos = [];

    // Visit context (optional)
    public $visitId;

    protected $queryString = [
        'visitId' => ['except' => null, 'as' => 'visit_id'],
    ];

    protected function rules()
    {
        $rules = [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'sede_id' => 'required|exists:sedes,id',
        ];

        if ($this->currentStep >= 2) {
            $rules['criticidad'] = 'required|in:bajo,medio,alto,critico';
            $rules['estado'] = 'required|in:pendiente,en_proceso,resuelto,verificado,cerrado';
            $rules['plazo_correccion'] = 'required|date|after:today';
            $rules['responsable_correccion'] = 'nullable|string|max:255';
        }

        if ($this->currentStep >= 3 && $this->origen_type) {
            $rules['origen_id'] = 'required|integer';
        }

        return $rules;
    }

    protected $messages = [
        'titulo.required' => 'El título es obligatorio',
        'descripcion.required' => 'La descripción es obligatoria',
        'sede_id.required' => 'Debes seleccionar una sede',
        'criticidad.required' => 'Debes seleccionar una criticidad',
        'plazo_correccion.required' => 'El plazo de corrección es obligatorio',
        'plazo_correccion.after' => 'El plazo debe ser posterior a hoy',
    ];

    public function mount($id = null)
    {
        // Capture visit_id from query string
        $this->visitId = request()->query('visit_id', $this->visitId);

        if ($id) {
            $this->hallazgoId = $id;
            $this->loadHallazgo();
        } else {
            // Default values
            $this->plazo_correccion = now()->addDays(15)->format('Y-m-d');
            
            // Pre-fill from visit context if available
            if ($this->visitId) {
                $visit = \App\Models\VisitCertificate::find($this->visitId);
                if ($visit) {
                    $this->sede_id = $visit->sede_id;
                }
                // Reduce steps: skip "Origen" step when from visit
                $this->totalSteps = 2; // Solo 2 pasos (Información, Clasificación)
            }
        }
    }

    public function loadHallazgo()
    {
        $hallazgo = Hallazgo::findOrFail($this->hallazgoId);

        $this->titulo = $hallazgo->titulo;
        $this->descripcion = $hallazgo->descripcion;
        $this->ubicacion_especifica = $hallazgo->ubicacion_especifica;
        $this->sede_id = $hallazgo->sede_id;
        $this->criticidad = $hallazgo->criticidad;
        $this->estado = $hallazgo->estado;
        $this->plazo_correccion = $hallazgo->plazo_correccion?->format('Y-m-d');
        $this->responsable_correccion = $hallazgo->responsable_correccion;
        $this->origen_type = $hallazgo->origen_type;
        $this->origen_id = $hallazgo->origen_id;

        // Inferir origen si fue creado desde una visita
        if (!$this->origen_type && $hallazgo->visit_certificate_id) {
            $this->origen_type = 'App\Models\VisitCertificate';
            $this->origen_id = $hallazgo->visit_certificate_id;
        }
    }

    public function nextStep()
    {
        $this->validateCurrentStep();

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep($step)
    {
        if ($step <= $this->currentStep || $step == 1) {
            $this->currentStep = $step;
        }
    }

    protected function validateCurrentStep()
    {
        if ($this->currentStep == 1) {
            $this->validate([
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'ubicacion_especifica' => 'nullable|string|max:255',
                'sede_id' => 'required|exists:sedes,id',
            ]);
        } elseif ($this->currentStep == 2) {
            $this->validate([
                'criticidad' => 'required|in:bajo,medio,alto,critico',
                'estado' => 'required|in:pendiente,en_proceso,resuelto,verificado,cerrado',
                'plazo_correccion' => 'required|date|after:today',
                'responsable_correccion' => 'nullable|string|max:255',
            ]);
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'ubicacion_especifica' => $this->ubicacion_especifica,
            'sede_id' => $this->sede_id,
            'user_id' => Auth::id(),
            'criticidad' => $this->criticidad,
            'estado' => $this->estado,
            'plazo_correccion' => $this->plazo_correccion,
            'responsable_correccion' => $this->responsable_correccion,
            'fecha_deteccion' => now(),
        ];

        // Origen (opcional)
        if ($this->origen_type && $this->origen_id) {
            if ($this->origen_type === 'App\Models\VisitCertificate') {
                $data['visit_certificate_id'] = $this->origen_id;
                $data['origen_type'] = null;
                $data['origen_id'] = null;
            } else {
                $data['origen_type'] = $this->origen_type;
                $data['origen_id'] = $this->origen_id;
            }
        }

        // Visit association (if created from visit context)
        if ($this->visitId) {
            $data['visit_certificate_id'] = $this->visitId;
        }

        if ($this->hallazgoId) {
            // Actualizar
            $hallazgo = Hallazgo::findOrFail($this->hallazgoId);
            $hallazgo->update($data);
            session()->flash('message', 'Hallazgo actualizado correctamente.');
        } else {
            // Crear
            $hallazgo = Hallazgo::create($data);
            session()->flash('message', 'Hallazgo creado correctamente.');
        }

        // Redirect back to visit if created from visit context
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $prefix = $user->isAdmin() ? 'gestor' : 'tecnico';
        if ($this->visitId) {
            return redirect()->route($prefix . '.visits.in-progress', $this->visitId);
        }

        return redirect()->route($prefix . '.hallazgos.show', $hallazgo);
    }

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Si es técnico, solo ve sus sedes asignadas. Si es admin, ve todas.
        $sedes = $user->isTechnician() 
            ? $user->sedes()->orderBy('nombre_sede')->get()
            : Sede::orderBy('nombre_sede')->get();

        $inspecciones = $this->sede_id 
            ? DynamicInspection::where('sede_id', $this->sede_id)->orderBy('created_at', 'desc')->get()
            : collect();
            
        $constancias = $this->sede_id 
            ? GeneradorConstancia::where('sede_id', $this->sede_id)->orderBy('created_at', 'desc')->get()
            : collect();

        $visitas = $this->sede_id 
            ? \App\Models\VisitCertificate::where('sede_id', $this->sede_id)->orderBy('created_at', 'desc')->get()
            : collect();

        return view('livewire.hallazgos.hallazgo-wizard', [
            'sedes' => $sedes,
            'inspecciones' => $inspecciones,
            'constancias' => $constancias,
            'visitas' => $visitas,
        ]);
    }
}
