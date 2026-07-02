<?php

namespace App\Services;

use App\Models\ChecklistAssignment;
use App\Models\Sede;
use App\Models\Training;
use App\Models\User;
use App\Models\VisitCertificate;
use App\Models\VisitChecklistCompletion;
use App\Models\VisitTraining;
use Illuminate\Support\Facades\DB;

class VisitService
{
    protected TimeTrackingService $timeTrackingService;

    public function __construct(TimeTrackingService $timeTrackingService)
    {
        $this->timeTrackingService = $timeTrackingService;
    }

    /**
     * Iniciar una nueva visita
     */
    public function startVisit(
        User $user,
        Sede $sede,
        ?string $visitType = null,
        ?float $checkinLat = null,
        ?float $checkinLng = null
    ): VisitCertificate {
        $visit = VisitCertificate::create([
            'user_id' => $user->id,
            'sede_id' => $sede->id,
            'empresa_id' => $sede->empresa_id,
            'visit_date' => now()->toDateString(),
            'start_time' => now(),
            'status' => 'in_progress',
            'visit_type' => $visitType,
            'checkin_lat' => $checkinLat,
            'checkin_lng' => $checkinLng,
        ]);

        $visit->setRelation('sede', $sede);
        $this->timeTrackingService->verifyGpsPresence($visit);
        $visit->save();

        return $visit;
    }

    /**
     * Registrar la completación de un checklist dentro de la visita
     */
    public function completeChecklist(
        VisitCertificate $visit,
        ChecklistAssignment $assignment,
        array $responses,
        bool $isSelfAssigned = false
    ): VisitChecklistCompletion {
        return DB::transaction(function () use ($visit, $assignment, $responses, $isSelfAssigned) {
            // 1. Crear registro de completación vinculado a la visita
            $completion = VisitChecklistCompletion::create([
                'visit_certificate_id' => $visit->id,
                'checklist_assignment_id' => $assignment->id,
                'is_self_assigned' => $isSelfAssigned,
                'completed_at' => now(),
                'responses' => $responses,
            ]);

            // 2. Actualizar estado de la asignación
            $assignment->update([
                'status' => 'completed',
            ]);

            return $completion;
        });
    }

    /**
     * Registrar una capacitación impartida durante la visita
     */
    public function addTraining(
        VisitCertificate $visit,
        ?Training $trainingTemplate, // NULL si es ad-hoc
        array $data, // title (si ad-hoc), description, duration, attendees
        bool $isAdHoc = false
    ): VisitTraining {
        return VisitTraining::create([
            'visit_certificate_id' => $visit->id,
            'training_id' => $trainingTemplate?->id,
            'is_adhoc' => $isAdHoc,
            'title' => $isAdHoc ? ($data['title'] ?? 'Capacitación Ad-hoc') : $trainingTemplate->title,
            'description' => $data['description'] ?? $trainingTemplate?->description,
            'duration_minutes' => $data['duration'] ?? $trainingTemplate?->duration,
            'attendees' => $data['attendees'] ?? [],
            'materials_delivered' => $data['materials_delivered'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    /**
     * Finalizar la visita y generar constancia
     */
    public function finalizeVisit(
        VisitCertificate $visit,
        ?string $observations,
        ?string $signature,
        ?string $responsibleName = null,
        ?string $emailResponsable = null,
        ?string $signatureSkipReason = null,
        ?float $checkoutLat = null,
        ?float $checkoutLng = null
    ): VisitCertificate {
        $visit->fill([
            'end_time' => now(),
            'general_observations' => $observations,
            'signature' => $signature,
            'responsible_name' => $responsibleName,
            'email_responsable' => $emailResponsable,
            'signature_skip_reason' => $signatureSkipReason,
            'status' => 'signed',
        ]);

        if ($checkoutLat !== null) {
            $visit->checkout_lat = $checkoutLat;
        }
        if ($checkoutLng !== null) {
            $visit->checkout_lng = $checkoutLng;
        }

        $visit->loadMissing('sede');
        $this->timeTrackingService->verifyGpsPresence($visit);
        $visit->save();

        return $visit;
    }

    /**
     * Obtener visita activa del usuario si existe
     */
    public function getActiveVisit(User $user): ?VisitCertificate
    {
        return VisitCertificate::with('sede')
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->latest()
            ->first();
    }
}
