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
    
    // Alertas de duración
    public $durationMinutes = 0;
    public $isVisitaExcedida = false;
    public $dismissedAlertsCount = 0;
    public const ALERT_THRESHOLD = 30; // 30 minutos

    
    // Colecciones
    public $assignedChecklists = [];
    public $completedChecklists = [];
    public $checklists = [];
    public $trainings = [];
    public $assignedTrainings = [];
    public $hallazgos = [];
    public $incidentes = [];


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



    protected $listeners = [
        'checklistCompleted' => 'onChecklistCompleted',
        'closeChecklistFiller' => 'closeFillModal',
        'trainingAdded' => 'onTrainingAdded',
        'closeTrainingAdHoc' => 'closeTrainingModal',

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
        
        $this->dismissedAlertsCount = session()->get('dismissed_visit_alerts_' . $this->visit->id, 0);
        $this->loadChecklists();
        $this->calculateDuration();
    }

    public function calculateDuration()
    {
        if ($this->visit && $this->visit->start_time) {
            $this->durationMinutes = (int) $this->visit->start_time->diffInMinutes(now());
            
            // Determinar en qué bloque de 30 minutos estamos
            $currentBlock = (int) floor($this->durationMinutes / self::ALERT_THRESHOLD);
            
            // Cargar desde sesión por si acaso (aunque ya se hace en mount)
            $this->dismissedAlertsCount = session()->get('dismissed_visit_alerts_' . $this->visit->id, 0);
            
            // Mostrar alerta si estamos en un bloque > 0 y no ha sido descartado para este bloque
            if ($currentBlock > 0 && $currentBlock > $this->dismissedAlertsCount) {
                $this->isVisitaExcedida = true;
            } else {
                $this->isVisitaExcedida = false;
            }
        }
    }

    public function dismissAlert()
    {
        // Marcar el bloque actual como descartado
        $this->dismissedAlertsCount = (int) floor($this->durationMinutes / self::ALERT_THRESHOLD);
        
        // Guardar en sesión para persistir entre navegaciones
        session()->put('dismissed_visit_alerts_' . $this->visit->id, $this->dismissedAlertsCount);
        
        $this->isVisitaExcedida = false;
    }

    public function loadChecklists()
    {
        // 1. Cargar asignaciones pendientes para esta sede/empresa
        $this->assignedChecklists = ChecklistAssignment::query()
            ->where('user_id', Auth::id())
            ->where('sede_id', $this->visit->sede_id)
            ->whereIn('status', ['pending', 'in_progress']) // Pendientes o en progreso
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
            ->whereDate('date', '<=', now())
            ->orderBy('date', 'asc')
            ->get();
            
        // 4. Cargar hallazgos registrados en la visita
        $this->hallazgos = $this->visit->hallazgos()->latest()->get();

        // 5. Cargar incidentes registrados en la visita
        $this->incidentes = $this->visit->incidentes()->latest()->get();
    }

    public function executeTraining($trainingId)
    {
        $training = \App\Models\Training::find($trainingId);
        
        if (!$training) {
            return;
        }

        // Evitar duplicados: Si ya existe una entrada de esta capacitación para esta visita,
        // simplemente la recuperamos o actualizamos en lugar de crear una nueva.
        $visitTraining = $this->visit->trainings()->updateOrCreate(
            ['training_id' => $training->id], // Criterio de búsqueda
            [
                'is_adhoc' => false,
                'title' => $training->title,
                'description' => $training->description,
                'duration_minutes' => $training->duration,
                'notes' => null // Dejar nulo para que use las notas del Training al finalizarse
            ]
        );

        // Redirigir a toma de asistencia con visit_id
        $prefix = auth()->user()->isAdmin() ? 'gestor' : 'tecnico';
        return redirect()->route($prefix . '.trainings.attendance', [
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
        $assignment = ChecklistAssignment::find($assignmentId);

        if (!$assignment || $assignment->user_id !== Auth::id()) {
            return;
        }

        // Guardar visit_id en sesión para que el wizard pueda leerlo en mount()
        session()->put('wizard_visit_id', $this->visit->id);

        // Redirigir al wizard de inspección con contexto de visita
        // assignment ahora es un parámetro de ruta, no query string
        $prefix = auth()->user()->isAdmin() ? 'gestor' : 'tecnico';
        return redirect()->route($prefix . '.checklists.fill', [
            'id'         => $assignment->dynamic_checklist_id,
            'assignment' => $assignment->id,
        ]);
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
        $prefix = auth()->user()->isAdmin() ? 'gestor' : 'tecnico';
        return redirect()->route($prefix . '.trainings.create', ['visit_id' => $this->visit->id]);
    }

    public function openAttendance(\App\Models\VisitTraining $visitTraining)
    {
        // Si es una capacitación vinculada (template), ir al tracker
        if ($visitTraining->training_id) {
            $prefix = auth()->user()->isAdmin() ? 'gestor' : 'tecnico';
            return redirect()->route($prefix . '.trainings.attendance', $visitTraining->training_id);
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

    public function removeTraining($id)
    {
        $training = $this->visit->trainings()->findOrFail($id);
        $training->delete();
        
        $this->loadChecklists();
        session()->flash('message', 'Registro de capacitación eliminado.');
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
