<?php

namespace App\Livewire\Trainings;

use Livewire\Component;

use App\Models\Training;
use App\Models\Empresa;
use App\Models\Sede;
use Illuminate\Validation\Rule;

class TrainingBuilder extends Component
{
    public $trainingId;
    public $title, $description, $type = 'presencial', $date, $duration, $trainer_name, $status = 'scheduled';
    public $empresa_id, $sede_id, $user_id, $dynamic_checklist_id;
    public $visitId; // ID de visita (opcional)

    protected $queryString = [
        'visitId' => ['except' => null, 'as' => 'visit_id'],
    ];

    public function mount($id = null)
    {
        // Capturar visit_id desde request si no llega por property (aunque queryString lo maneja)
        $this->visitId = request()->query('visit_id', $this->visitId);

        if ($id) {
            $training = Training::findOrFail($id);
            $this->trainingId = $training->id;
            $this->title = $training->title;
            // ... resto de campos ...
            $this->description = $training->description;
            $this->type = $training->type;
            $this->date = $training->date->format('Y-m-d\TH:i');
            $this->duration = $training->duration;
            $this->trainer_name = $training->trainer_name;
            $this->status = $training->status;
            $this->empresa_id = $training->empresa_id;
            $this->sede_id = $training->sede_id;
            $this->user_id = $training->user_id;
            $this->dynamic_checklist_id = $training->dynamic_checklist_id;
        } else if ($this->visitId) {
            // Pre-llenar desde visita
            $visit = \App\Models\VisitCertificate::find($this->visitId);
            if ($visit) {
                $this->empresa_id = $visit->empresa_id;
                $this->sede_id = $visit->sede_id;
                $this->date = now()->format('Y-m-d\TH:i'); // Default to now
            }
        }
    }

    public function updatedDynamicChecklistId($value)
    {
        if ($value && empty($this->title)) {
            $checklist = \App\Models\DynamicChecklist::find($value);
            if ($checklist) {
                $this->title = $checklist->title;
            }
        }
    }

    public function rules()
    {
        return [
            'dynamic_checklist_id' => 'required|exists:dynamic_checklists,id',
            'title' => 'required|string|max:255',
            'empresa_id' => 'required|exists:empresas,id',
            'sede_id' => 'required|exists:sedes,id',
            'user_id' => 'nullable|exists:users,id', // Responsable técnico de la DB
            'type' => 'required|in:presencial,virtual',
            'date' => 'required|date',
            'duration' => 'nullable|integer|min:1',
            'trainer_name' => 'nullable|string|max:255',
            'status' => 'required|in:scheduled,completed,cancelled',
            'description' => 'nullable|string',
        ];
    }

    public function save()
    {
        $data = $this->validate();

        if ($this->trainingId) {
            $training = Training::find($this->trainingId);
            $training->update($data);
            session()->flash('message', 'Capacitación actualizada correctamente.');
        } else {
            $training = Training::create($data);
            
            // Si viene de una visita, linkear
            if ($this->visitId) {
                \App\Models\VisitTraining::create([
                    'visit_certificate_id' => $this->visitId,
                    'training_id' => $training->id,
                    'is_adhoc' => false,
                ]);
            }
            session()->flash('message', 'Capacitación creada correctamente.');
        }

        if ($this->visitId) {
            $prefix = auth()->user()->hasRole('Administrador') ? 'gestor' : 'tecnico';
            return redirect()->route($prefix . '.visits.in-progress', $this->visitId);
        }

        if (auth()->user()->isAdmin()) {
            return redirect()->route('gestor.trainings.index');
        }

        // Si es técnico, redirigir al dashboard o a la toma de asistencia si acaba de crearla
        return redirect()->route('tecnico.dashboard');
    }

    public function render()
    {
        $empresas = Empresa::orderBy('nombre')->get();
        $sedes = $this->empresa_id 
            ? Sede::where('empresa_id', $this->empresa_id)->orderBy('nombre_sede')->get() 
            : collect();

        $tecnicos = $this->sede_id
            ? Sede::find($this->sede_id)->users()->orderBy('apellidos')->get()
            : collect();

        $checklists = \App\Models\DynamicChecklist::active()->orderBy('title')->get();

        return view('livewire.trainings.training-builder', [
            'empresas' => $empresas,
            'sedes' => $sedes,
            'tecnicos' => $tecnicos,
            'checklists' => $checklists,
        ])->layout('components.layouts.app');
    }
}
