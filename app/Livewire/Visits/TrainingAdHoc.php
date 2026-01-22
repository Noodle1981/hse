<?php

namespace App\Livewire\Visits;

use App\Models\VisitCertificate;
use App\Models\Training;
use App\Models\VisitTraining;
use App\Services\VisitService;
use Livewire\Component;
use Livewire\WithFileUploads;

class TrainingAdHoc extends Component
{
    use WithFileUploads;

    public VisitCertificate $visit;
    public $showModal = false;

    // Form fields
    public $title;
    public $description;
    public $duration_minutes = 30; // Minutos
    public $attendees = []; // [{name, signature}] - Simplified to array of names for MVP
    public $attendeesText = ''; // Para input textarea
    public $photo_evidence;

    // Template selection
    public $selectedTemplateId = null;
    public $templates = [];

    protected $visitService;

    public function boot(VisitService $visitService)
    {
        $this->visitService = $visitService;
    }

    public function mount(VisitCertificate $visit)
    {
        $this->visit = $visit;
        $this->loadTemplates();
    }

    public function loadTemplates()
    {
        $this->templates = Training::where('is_active', true)->orderBy('title')->get();
    }

    public function updatedSelectedTemplateId($value)
    {
        if ($value) {
            $template = Training::find($value);
            if ($template) {
                $this->title = $template->title;
                $this->description = $template->description;
                $this->duration_minutes = $template->duration_minutes;
            }
        }
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration_minutes' => 'required|integer|min:5',
            'attendeesText' => 'required|string',
        ]);

        // Procesar asistentes desde textarea (uno por línea)
        $attendeeNames = array_filter(array_map('trim', explode("\n", $this->attendeesText)));

        // Guardar usando el servicio
        $training = $this->visitService->addTraining(
            $this->visit,
            $this->selectedTemplateId ? Training::find($this->selectedTemplateId) : null,
            [
                'title' => $this->title,
                'description' => $this->description,
                'duration' => $this->duration_minutes,
                'attendees' => $attendeeNames,
                // 'notes' => ...
            ],
            isAdHoc: is_null($this->selectedTemplateId)
        );

        // Subir foto evidencia (TODO: Integración real de storage)
        if ($this->photo_evidence) {
            // $path = $this->photo_evidence->store('trainings', 'public');
            // $training->update(['evidence_photo' => $path]);
        }

        // Emitir evento para cerrar y recargar
        $this->dispatch('trainingAdded');
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->duration_minutes = 30;
        $this->attendeesText = '';
        $this->selectedTemplateId = null;
        $this->photo_evidence = null;
    }

    public function close()
    {
        $this->dispatch('closeTrainingAdHoc');
    }

    public function render()
    {
        return view('livewire.visits.training-ad-hoc');
    }
}
