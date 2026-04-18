<?php

namespace App\Livewire\Trainings;

use App\Models\Training;
use App\Models\TrainingAttendance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class AttendanceTracker extends Component
{
    public Training $training;
    public $attendees = [];
    public $observations = '';
    public $visitId = null; 
    public $newAttendee = [
        'employee_name' => '',
        'dni' => '',
        'signature' => '',
    ];

    public function mount($id)
    {
        $this->training = Training::with(['empresa', 'sede'])->findOrFail($id);
        $this->visitId = request()->query('visit_id');
        $this->loadAttendees();
    }

    public function loadAttendees()
    {
        $this->attendees = TrainingAttendance::where('training_id', $this->training->id)
            ->latest()
            ->get();
    }

    public function addAttendance()
    {
        $this->validate([
            'newAttendee.employee_name' => 'required|string|max:255',
            'newAttendee.dni' => ['required', 'regex:/^\d{7,8}$/'], // DNI argentino: 7-8 dígitos
            'newAttendee.signature' => 'required|string', // Base64
        ], [
            'newAttendee.employee_name.required' => 'El nombre es obligatorio',
            'newAttendee.dni.required' => 'El DNI es obligatorio',
            'newAttendee.dni.regex' => 'El DNI debe tener 7 u 8 dígitos',
            'newAttendee.signature.required' => 'La firma es obligatoria',
        ]);

        // Prevenir duplicados
        $exists = TrainingAttendance::where('training_id', $this->training->id)
            ->where('dni', $this->newAttendee['dni'])
            ->exists();

        if ($exists) {
            $this->addError('newAttendee.dni', 'Esta persona ya registró su asistencia a esta capacitación.');
            return;
        }

        TrainingAttendance::create([
            'training_id' => $this->training->id,
            'employee_name' => $this->newAttendee['employee_name'],
            'dni' => $this->newAttendee['dni'],
            'signature' => $this->newAttendee['signature'],
        ]);

        $this->newAttendee = [
            'employee_name' => '',
            'dni' => '',
            'signature' => '',
        ];

        $this->loadAttendees();
        $this->dispatch('attendance-added');
        session()->flash('message', 'Asistencia registrada correctamente.');
    }

    public function completeTraining()
    {
        // Actualizar observaciones y estado
        $this->training->update([
            'status' => 'completed',
            'notes' => $this->observations,
        ]);

        session()->flash('message', 'Capacitación finalizada correctamente.');
        
        // Si vino desde una visita, regresar a ella respetando el rol del usuario
        if ($this->visitId) {
            $user = Auth::user();
            $routeName = ($user instanceof User && $user->isAdmin()) ? 'gestor.visits.in-progress' : 'tecnico.visits.in-progress';
            return redirect()->route($routeName, $this->visitId);
        }
        
        // Sino, redirigir según rol
        $user = Auth::user();
        if ($user instanceof User && $user->isAdmin()) {
            return redirect()->route('gestor.trainings.index');
        }
        return redirect()->route('tecnico.dashboard');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.trainings.attendance-tracker');
    }
}
