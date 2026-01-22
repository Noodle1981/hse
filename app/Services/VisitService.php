<?php

namespace App\Services;

use App\Models\ChecklistAssignment;
use App\Models\Hallazgo;
use App\Models\Incidente;
use App\Models\Sede;
use App\Models\Training;
use App\Models\User;
use App\Models\VisitCertificate;
use App\Models\VisitChecklistCompletion;
use App\Models\VisitEppDelivery;
use App\Models\VisitTraining;
use Illuminate\Support\Facades\DB;

class VisitService
{
    /**
     * Iniciar una nueva visita
     */
    public function startVisit(User $user, Sede $sede): VisitCertificate
    {
        return VisitCertificate::create([
            'user_id' => $user->id,
            'sede_id' => $sede->id,
            'empresa_id' => $sede->empresa_id,
            'visit_date' => now()->toDateString(),
            'start_time' => now(),
            'status' => 'in_progress',
        ]);
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
                'completed_at' => now(), // Aseguramos que el modelo tenga este campo o usamos updated_at
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
     * Registrar una entrega de EPP durante la visita
     */
    public function deliverEpp(
        VisitCertificate $visit,
        int $employeeId,
        array $items, // [{item, qty, size}]
        ?string $signature = null,
        ?string $notes = null
    ): VisitEppDelivery {
        return VisitEppDelivery::create([
            'visit_certificate_id' => $visit->id,
            'employee_id' => $employeeId,
            'epp_items' => $items,
            'employee_signature' => $signature,
            'notes' => $notes,
        ]);
    }

    /**
     * Finalizar la visita y generar constancia
     */
    public function finalizeVisit(
        VisitCertificate $visit,
        ?string $observations,
        ?string $signature
    ): VisitCertificate {
        $visit->update([
            'end_time' => now(),
            'general_observations' => $observations,
            'signature' => $signature,
            'status' => 'signed',
        ]);

        // Aquí se podría disparar un evento para generar PDF o notificar
        // event(new VisitCompleted($visit));

        return $visit;
    }

    /**
     * Obtener visita activa del usuario si existe
     */
    public function getActiveVisit(User $user): ?VisitCertificate
    {
        return VisitCertificate::where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->latest()
            ->first();
    }
}
