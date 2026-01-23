<?php

namespace App\Livewire\Visits;

use App\Models\ChecklistAssignment;
use App\Models\VisitCertificate;
use App\Models\DynamicChecklist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\ConstanciaFirmaResponsable;
use Livewire\Component;

class VisitInProgress extends Component
{
    public VisitCertificate $visit;
    
    // Colecciones
    public $assignedChecklists = [];
    public $completedChecklists = [];
    public $checklists = [];
    public $trainings = [];
    public $assignedTrainings = [];
    public $eppDeliveries = [];
    public $hallazgos = [];
    
    // EPP Requests
    public $assignedEppRequests = [];
    public $selectedEppRequestId = null;

    public function updatedSignatureUnavailable($value)
    {
        if ($value && empty($this->responsibleEmail)) {
            // Intentar pre-llenar con el email del responsable de la sede
            $sede = $this->visit->sede;
            if ($sede) {
                // Priorizar responsable 1, luego 2, luego 3
                $this->responsibleEmail = $sede->responsable_1_email 
                    ?? $sede->responsable_2_email 
                    ?? $sede->responsable_3_email 
                    ?? $sede->correo 
                    ?? '';
                
                // También intentar pre-llenar el nombre si está vacío
                if (empty($this->responsibleName)) {
                    $this->responsibleName = $sede->responsable_1_nombre
                        ?? $sede->responsable_2_nombre
                        ?? $sede->responsable_3_nombre
                        ?? '';
                }
            }
        }
    }

    // ... (listeners)

    // ... (mount)



    public function executeEppRequest($requestId)
    {
        $this->selectedEppRequestId = $requestId;
        $this->showEppModal = true;
    }

    public function openEppModal()
    {
        $this->selectedEppRequestId = null;
        $this->showEppModal = true;
    }

    public function closeEppModal()
    {
        $this->showEppModal = false;
        $this->selectedEppRequestId = null;
    }

    public function onEppDelivered()
    {
        // Si venía de una request, actualizar estado
        if ($this->selectedEppRequestId) {
            $eppRequest = \App\Models\EppDeliveryRequest::find($this->selectedEppRequestId);
            if ($eppRequest) {
                $eppRequest->update(['status' => 'completed']);
            }
        }

        $this->closeEppModal();
        $this->loadChecklists(); // Recargar todo
        session()->flash('message', 'EPP entregado exitosamente.');
    }

    // Estado de modales
    public $showAutoAssignModal = false;
    public $showFinalizeModal = false;
    public $showHallazgoModal = false;
    public $showIncidenteModal = false;
    
    // Finalización de visita
    public $visitNotes = '';
    public $responsibleName = '';
    public $responsibleSignature = '';
    
    // Firma Remota
    public $signatureUnavailable = false;
    public $signatureUnavailableReason = '';
    public $responsibleEmail = '';
    
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
        
        // Verificar que el usuario sea el dueño o Admin
        if ($this->visit->user_id !== Auth::id() && !Auth::user()->hasRole('Administrador')) {
            abort(403);
        }
        
        // Verificar que esté en progreso
        if ($this->visit->status !== 'in_progress') {
            if (Auth::user()->hasRole('Administrador')) {
                return redirect()->route('gestor.visits.history');
            }
            return redirect()->route('tecnico.visits.index');
        }
        
        $this->loadChecklists();
    }

    public function loadChecklists()
    {
        // 1. Cargar asignaciones pendientes para esta sede/empresa
        $this->assignedChecklists = ChecklistAssignment::query()
            ->where('user_id', Auth::id())
            ->where('sede_id', $this->visit->sede_id)
            ->where('status', 'pending') // Solo pendientes
            ->with(['checklist.categoryRelation', 'checklist.subcategoryRelation'])
            ->get();

        // 2. Cargar checklists ya completados en ESTA visita
        $this->completedChecklists = $this->visit->checklistCompletions()
            ->with(['assignment.checklist'])
            ->get();
            
        // 3. Cargar capacitaciones realizadas
        $this->trainings = $this->visit->trainings()->with('training')->latest()->get();

        // 3.1 Cargar capacitaciones asignadas (pendientes) para esta sede y usuario
        $this->assignedTrainings = \App\Models\Training::query()
            ->where('user_id', Auth::id())
            ->where('sede_id', $this->visit->sede_id)
            ->where('status', 'scheduled') // Solo programadas
            ->orderBy('date', 'asc')
            ->get();

        // 3.2 Cargar Solicitudes de EPP (Pendientes)
        $this->assignedEppRequests = \App\Models\EppDeliveryRequest::where('sede_id', $this->visit->sede_id)
            ->where('status', 'pending')
            ->with(['employee']) 
            ->get();

        // 4. Cargar entregas de EPP realizadas
        $this->eppDeliveries = $this->visit->eppDeliveries()->with('employee')->latest()->get();

        // 5. Cargar hallazgos
        $this->hallazgos = $this->visit->hallazgos()->latest()->get();
    }

    public function executeTraining($trainingId)
    {
        $training = \App\Models\Training::find($trainingId);
        
        if (!$training) {
            return;
        }

        // Crear registro de capacitación en la visita vinculado a la planificación
        $visitTraining = $this->visit->trainings()->create([
            'training_id' => $training->id, // Vinculamos al ID original
            'is_adhoc' => false,
            'title' => $training->title,
            'description' => $training->description,
            'duration_minutes' => $training->duration,
            'attendees' => [], // Inicialmente vacío
            'notes' => 'Ejecutado desde Visita #' . $this->visit->id
        ]);

        // Redirigir a toma de asistencia con visit_id
        return redirect()->route('trainings.attendance', [
            'id' => $training->id,
            'visit_id' => $this->visit->id
        ]);
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
        // Si es ad-hoc (sin ID de training padre), quizás necesitemos otra vista o no soportarlo aún
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

    public function finalizeVisit()
    {
        // Validar campos requeridos
        $rules = [
            'visitNotes' => 'required|string|min:10',
            'responsibleName' => 'required|string|min:3',
        ];

        if ($this->signatureUnavailable) {
            $rules['signatureUnavailableReason'] = 'required|string|min:5';
            $rules['responsibleEmail'] = 'nullable|email';
        } else {
            $rules['responsibleSignature'] = 'required|string';
        }

        $this->validate($rules, [
            'visitNotes.required' => 'Las observaciones son obligatorias',
            'visitNotes.min' => 'Las observaciones deben tener al menos 10 caracteres',
            'responsibleName.required' => 'El nombre del responsable es obligatorio',
            'responsibleName.min' => 'El nombre debe tener al menos 3 caracteres',
            'responsibleSignature.required' => 'La firma digital es obligatoria',
            'signatureUnavailableReason.required' => 'Debe indicar el motivo por el cual no se pudo obtener la firma',
            'responsibleEmail.email' => 'El formato del email no es válido',
        ]);

        // Datos básicos
        $data = [
            'end_time' => now(),
            'general_observations' => $this->visitNotes,
            'responsible_name' => $this->responsibleName,
        ];

        if ($this->signatureUnavailable) {
            $data['status'] = 'completed'; // Usamos completed ya que pending_signature no existe en el enum de la DB
            $data['signature_skip_reason'] = $this->signatureUnavailableReason;
            $data['email_responsable'] = $this->responsibleEmail;

            // Generar token y código si hay email
            if ($this->responsibleEmail) {
                $data['token_responsable'] = Str::random(60);
                $data['code'] = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            }
        } else {
            $data['status'] = 'completed';
            $data['signature'] = $this->responsibleSignature;
            $data['date_firma_responsable'] = now();
        }

        // Actualizar estado de la visita
        $this->visit->update($data);

        // Enviar email si aplica
        if ($this->signatureUnavailable && $this->responsibleEmail) {
            try {
                // Usamos el mismo Mailable, pero asegurandonos que acepte VisitCertificate
                // VisitCertificate debe tener las props esperadas por la vista del mail.
                Mail::to($this->responsibleEmail)->send(new ConstanciaFirmaResponsable($this->visit));
            } catch (\Exception $e) {
                session()->flash('warning', 'Visita finalizada pero error enviando email: ' . $e->getMessage());
            }
        }

        session()->flash('message', 'Visita finalizada correctamente.');
        
        // Redirigir a lista de visitas
        if (auth()->user()->hasRole('Administrador')) {
            return redirect()->route('gestor.visits.history');
        }
        return redirect()->route('tecnico.visits.index');
    }

    public function render()
    {
        return view('livewire.visits.visit-in-progress')->layout('components.layouts.app');
    }
}
