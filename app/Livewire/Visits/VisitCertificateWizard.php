<?php

namespace App\Livewire\Visits;

use App\Models\GeneradorConstancia;
use App\Models\Sede;
use App\Models\DynamicInspection;
use App\Models\Training;

use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConstanciaFirmaResponsable;

class VisitCertificateWizard extends Component
{
    public $currentStep = 1;
    public $totalSteps = 4; // Aumentado a 4 pasos

    // Step 1: Sede, Tipo de Visita y Fecha
    public $sede_id = '';
    public $tipo_visita = '';
    public $fecha_visita = '';

    // Step 2: Observaciones
    public $observacion = '';

    // Step 3: Actividades
    public $selectedInspections = [];
    public $selectedTrainings = [];
    public $selectedHallazgos = [];

    // Step 4: Firmas (antes era Step 3)
    public $firma_instructor = '';
    public $email_responsable = '';
    public $aclaracion_responsable = '';
    public $firma_no_disponible = false;
    public $justificacion_firma = '';

    public ?GeneradorConstancia $certificate = null;

    public function updatedFirmaNoDisponible($value)
    {
        if ($value && empty($this->email_responsable) && !empty($this->sede_id)) {
            $sede = Sede::find($this->sede_id);
            if ($sede) {
                $this->email_responsable = $sede->responsable_1_email 
                    ?? $sede->responsable_2_email 
                    ?? $sede->responsable_3_email 
                    ?? $sede->correo 
                    ?? '';
                
                if (empty($this->aclaracion_responsable)) {
                    $this->aclaracion_responsable = $sede->responsable_1_nombre
                        ?? $sede->responsable_2_nombre
                        ?? $sede->responsable_3_nombre
                        ?? '';
                }
            }
        }
    }

    public function mount()
    {
        $this->fecha_visita = now()->format('Y-m-d');
    }

    protected $rules = [
        'sede_id' => 'required|exists:sedes,id',
        'tipo_visita' => 'required|string|max:255',
        'fecha_visita' => 'required|date|before_or_equal:today',
        'observacion' => 'required|string|max:3000',
        'firma_instructor' => 'required|string',
        'email_responsable' => 'nullable|email|max:255',
        'aclaracion_responsable' => 'nullable|string|max:255',
        'justificacion_firma' => 'required_if:firma_no_disponible,true|nullable|string|max:500',
    ];

    protected $messages = [
        'sede_id.required' => 'Debe seleccionar una sede',
        'tipo_visita.required' => 'El tipo de visita es obligatorio',
        'observacion.required' => 'Las observaciones son obligatorias',
        'firma_instructor.required' => 'La firma del técnico es obligatoria',
    ];

    public function nextStep()
    {
        $this->validateCurrentStep();
        
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function prevStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    protected function validateCurrentStep()
    {
        if ($this->currentStep === 1) {
            $this->validate([
                'sede_id' => 'required|exists:sedes,id',
                'tipo_visita' => 'required|string|max:255',
                'fecha_visita' => 'required|date|before_or_equal:today',
            ]);
        } elseif ($this->currentStep === 2) {
            $this->validate([
                'observacion' => 'required|string|max:3000',
            ]);
        }
    }

    public function complete()
    {
        $this->validate([
            'firma_instructor' => 'required|string',
            'email_responsable' => 'nullable|email|max:255',
            'aclaracion_responsable' => 'nullable|string|max:255',
        ]);

        // Generar token y código para firma del responsable
        $token = Str::random(60);
        $code = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        $this->certificate = GeneradorConstancia::create([
            'sede_id' => $this->sede_id,
            'user_id' => auth()->id(),
            'fecha_visita' => $this->fecha_visita,
            'tipo_visita' => $this->tipo_visita,
            'observacion' => $this->observacion,
            'firma_instructor' => $this->firma_instructor,
            'date_firma_instructor' => now(),
            'status' => 'completed', // Wizard always creates completed certificates
            'justificacion_firma' => $this->firma_no_disponible ? $this->justificacion_firma : null,
            'signature_skip_reason' => $this->firma_no_disponible ? $this->justificacion_firma : null, // Support both models if migrating
            'email_responsable' => $this->email_responsable,
            'aclaracion_responsable' => $this->aclaracion_responsable,
            'token_responsable' => $token,
            'code' => $code,
        ]);

        // Vincular actividades seleccionadas
        if (!empty($this->selectedInspections)) {
            $this->certificate->inspections()->attach($this->selectedInspections);
        }
        if (!empty($this->selectedTrainings)) {
            $this->certificate->trainings()->attach($this->selectedTrainings);
        }
        if (!empty($this->selectedHallazgos)) {
            $this->certificate->hallazgosActividades()->attach($this->selectedHallazgos);
        }

        // Enviar email al responsable si la firma no está disponible y hay email
        if ($this->firma_no_disponible && $this->email_responsable) {
            try {
                Mail::to($this->email_responsable)->send(new ConstanciaFirmaResponsable($this->certificate));
            } catch (\Exception $e) {
                // Log error but continue
                \Illuminate\Support\Facades\Log::error('Error enviando email de firma: ' . $e->getMessage());
                session()->flash('warning', 'Constancia creada pero hubo un error enviando el email: ' . $e->getMessage());
                return redirect()->route('visit-certificates.history');
            }
        }

        session()->flash('message', '¡Constancia de visita creada exitosamente!');
        
        return redirect()->route('visit-certificates.history');
    }

    public function getActivitiesForSede()
    {
        if (empty($this->sede_id)) {
            return [
                'inspections'    => collect(),
                'trainings'      => collect(),
                'hallazgos'      => collect(),
            ];
        }

        $visitDate = \Carbon\Carbon::parse($this->fecha_visita);
        $startDate = $visitDate->copy()->subDays(7)->startOfDay();
        $endDate = $visitDate->copy()->endOfDay();
        
        return [
            'inspections' => DynamicInspection::where('user_id', auth()->id())
                ->where('sede_id', $this->sede_id)
                ->whereBetween('completed_at', [$startDate, $endDate])
                ->with('checklist:id,title')
                ->get(),
            
            'trainings' => Training::where('sede_id', $this->sede_id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get(),
            

            'hallazgos' => \App\Models\Hallazgo::where('user_id', auth()->id())
                ->where('sede_id', $this->sede_id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get(),
        ];
    }

    public function render()
    {
        $user = auth()->user();
        
        // Filtrar sedes según el rol
        if ($user->rol === 'Administrador') {
            // Admin ve todas las sedes
            $sedes = Sede::with('empresa')
                ->orderBy('nombre_sede')
                ->get();
        } else {
            // Técnico solo ve sus sedes asignadas
            $sedes = $user->sedes()
                ->with('empresa')
                ->orderBy('nombre_sede')
                ->get();
        }

        return view('livewire.visits.visit-certificate-wizard', [
            'sedes' => $sedes,
        ])->layout('components.layouts.app');
    }
}
