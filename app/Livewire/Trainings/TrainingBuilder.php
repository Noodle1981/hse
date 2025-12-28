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
    public $empresa_id, $sede_id, $user_id;

    public function mount($id = null)
    {
        if ($id) {
            $training = Training::findOrFail($id);
            $this->trainingId = $training->id;
            $this->title = $training->title;
            $this->description = $training->description;
            $this->type = $training->type;
            $this->date = $training->date->format('Y-m-d\TH:i');
            $this->duration = $training->duration;
            $this->trainer_name = $training->trainer_name;
            $this->status = $training->status;
            $this->empresa_id = $training->empresa_id;
            $this->sede_id = $training->sede_id;
            $this->user_id = $training->user_id;
        }
    }

    public function rules()
    {
        return [
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
            Training::find($this->trainingId)->update($data);
            session()->flash('message', 'Capacitación actualizada correctamente.');
        } else {
            Training::create($data);
            session()->flash('message', 'Capacitación creada correctamente.');
        }

        return redirect()->route('trainings.index');
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

        return view('livewire.trainings.training-builder', [
            'empresas' => $empresas,
            'sedes' => $sedes,
            'tecnicos' => $tecnicos,
        ])->layout('components.layouts.app');
    }
}
