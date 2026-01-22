<?php

namespace App\Livewire\Visits;

use App\Models\ChecklistAssignment;
use App\Models\VisitCertificate;
use App\Models\DynamicChecklist;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class VisitInProgress extends Component
{
    public VisitCertificate $visit;
    
    // Colecciones
    public $assignedChecklists = [];
    public $completedChecklists = [];
    public $checklists = [];
    public $trainings = [];
    public $eppDeliveries = [];
    public $hallazgos = [];
    
    // Estado de modales
    public $showAutoAssignModal = false;
    public $showFinalizeModal = false;
    public $showHallazgoModal = false;
    public $showIncidenteModal = false;
    
    // Auto-asignación
    public $availableChecklists = [];
    public $selectedChecklistIdToAssign;

    // Llenado de Checklist
    public $showFillModal = false;
    public $selectedAssignmentToFill = null;

    // Capacitación Ad-Hoc
    public $showTrainingModal = false;

    // Entrega EPP
    public $showEppModal = false;

    protected $listeners = [
        'checklistCompleted' => 'onChecklistCompleted',
        'closeChecklistFiller' => 'closeFillModal',
        'trainingAdded' => 'onTrainingAdded',
        'closeTrainingAdHoc' => 'closeTrainingModal',
        'eppDelivered' => 'onEppDelivered',
        'closeEppDelivery' => 'closeEppModal',
        'hallazgoCreated' => 'onHallazgoCreated',
        'closeHallazgoQuick' => 'closeHallazgoModal',
        'incidenteCreated' => 'onIncidenteCreated',
        'closeIncidenteQuick' => 'closeIncidenteModal',
    ];

    public function mount(VisitCertificate $visit)
    {
        $this->visit = $visit;
        
        // Verificar que el usuario sea el dueño
        if ($this->visit->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Verificar que esté en progreso
        if ($this->visit->status !== 'in_progress') {
            return redirect()->route('visits.index');
        }
        
        $this->loadChecklists();
    }

    public function loadChecklists()
    {
        // 1. Cargar asignaciones pendientes para esta sede/empresa
        // This property is still used in openFillModal, so it needs to be kept or refactored.
        // For now, keeping it as the diff doesn't explicitly remove its usage.
        $this->assignedChecklists = ChecklistAssignment::query()
            ->where('user_id', Auth::id())
            ->where('sede_id', $this->visit->sede_id)
            ->where('status', 'pending') // Solo pendientes
            ->with(['checklist.categoryRelation', 'checklist.subcategoryRelation'])
            ->get();

        // 2. Cargar checklists ya completados en ESTA visita
        // This property is still used in openFillModal, so it needs to be kept or refactored.
        // For now, keeping it as the diff doesn't explicitly remove its usage.
        $this->completedChecklists = $this->visit->checklistCompletions()
            ->with(['assignment.checklist'])
            ->get();
            
        // The diff indicates a new 'checklists' property should hold completions.
        // Assuming this replaces the need for 'completedChecklists' for display purposes.
        $this->checklists = $this->visit->checklistCompletions()->with('assignment.checklist')->get();

        // 3. Cargar capacitaciones realizadas
        $this->trainings = $this->visit->trainings()->latest()->get();

        // 4. Cargar entregas de EPP realizadas
        $this->eppDeliveries = $this->visit->eppDeliveries()->with('employee')->latest()->get();

        // 5. Cargar hallazgos
        $this->hallazgos = $this->visit->hallazgos()->latest()->get();
    }

    public function openAutoAssignModal()
    {
        // Cargar checklists disponibles para auto-asignar (activos)
        // Podríamos filtrar por aquellos que no están ya asignados pendiente
        $this->availableChecklists = DynamicChecklist::where('is_active', true)
            ->with(['categoryRelation', 'subcategoryRelation'])
            ->orderBy('title')
            ->get();
            
        $this->showAutoAssignModal = true;
    }

    public function autoAssignChecklist()
    {
        $this->validate([
            'selectedChecklistIdToAssign' => 'required|exists:dynamic_checklists,id'
        ]);

        // Crear una nueva asignación para este usuario y sede
        // Status pending, frequency 'once' (ad-hoc)
        ChecklistAssignment::create([
            'dynamic_checklist_id' => $this->selectedChecklistIdToAssign,
            'user_id' => Auth::id(),
            'empresa_id' => $this->visit->empresa_id,
            'sede_id' => $this->visit->sede_id,
            'frequency' => 'once',
            'status' => 'pending',
            'due_date' => now(), // Vence hoy
            'notes' => 'Auto-asignado durante visita #' . $this->visit->id
        ]);

        $this->showAutoAssignModal = false;
        $this->selectedChecklistIdToAssign = null;
        $this->loadChecklists(); // Recargar lista
        
        // Notificar éxito (opcional)
        // session()->flash('message', 'Checklist auto-asignado correctamente.');
    }

    public function openFillModal($assignmentId)
    {
        $this->selectedAssignmentToFill = ChecklistAssignment::find($assignmentId);
        
        if ($this->selectedAssignmentToFill && $this->selectedAssignmentToFill->user_id === Auth::id()) {
            $this->showFillModal = true;
        }
    }

    public function closeFillModal()
    {
        $this->showFillModal = false;
        $this->selectedAssignmentToFill = null;
    }

    public function onChecklistCompleted($assignmentId)
    {
        $this->closeFillModal();
        $this->loadChecklists(); // Recargar listas para mover de pendiente a completado
        session()->flash('message', 'Checklist completado exitosamente.');
    }

    public function onTrainingAdded()
    {
        $this->closeTrainingModal();
        $this->loadChecklists(); // Recargar todo
        session()->flash('message', 'Capacitación registrada exitosamente.');
    }

    public function closeTrainingModal()
    {
        $this->showTrainingModal = false;
    }

    public function openTrainingModal()
    {
        // Redirigir al generador avanzado de capacitaciones (Admin Format)
        return redirect()->route('trainings.create', ['visit_id' => $this->visit->id]);
    }

    public function openAttendance(\App\Models\VisitTraining $visitTraining)
    {
        // Si es una capacitación vinculada (template), ir al tracker
        if ($visitTraining->training_id) {
            return redirect()->route('trainings.attendance', $visitTraining->training_id);
        }
    }

    public function openEppModal()
    {
        $this->showEppModal = true;
    }

    public function closeEppModal()
    {
        $this->showEppModal = false;
    }

    public function onEppDelivered()
    {
        $this->closeEppModal();
        $this->loadChecklists(); // Recargar todo
        session()->flash('message', 'EPP entregado exitosamente.');
    }

    public function openHallazgoModal()
    {
        $this->showHallazgoModal = true;
    }

    public function closeHallazgoModal()
    {
        $this->showHallazgoModal = false;
    }

    public function onHallazgoCreated()
    {
        $this->closeHallazgoModal();
        $this->loadChecklists();
        session()->flash('message', 'Hallazgo registrado exitosamente.');
    }

    public function openIncidenteModal()
    {
        $this->showIncidenteModal = true;
    }

    public function closeIncidenteModal()
    {
        $this->showIncidenteModal = false;
    }

    public function onIncidenteCreated()
    {
        $this->closeIncidenteModal();
        $this->loadChecklists();
        session()->flash('message', 'Incidente reportado exitosamente.');
    }

    public function render()
    {
        return view('livewire.visits.visit-in-progress')->layout('components.layouts.app');
    }
}
