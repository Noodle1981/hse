<?php

namespace App\Services;

use App\Models\DynamicInspection;
use App\Models\Hallazgo;
use App\Models\Incidente;
use App\Models\Training;
use App\Models\User;
use App\Models\VisitCertificate;
use App\Models\VisitChecklistCompletion;
use App\Models\VisitTraining;
use Illuminate\Database\Eloquent\Collection;

class PdfReportService
{
    public function loadVisitSummary($id): VisitCertificate
    {
        return VisitCertificate::with('sede')->findOrFail($id);
    }

    public function loadVisitFull($id): VisitCertificate
    {
        return VisitCertificate::with([
            'empresa',
            'sede',
            'user',
            'checklistCompletions.assignment.checklist',
            'checklistCompletions.assignment.inspection.asset.assetType',
            'trainings.training',
            'hallazgos.fotos',
            'incidentes.fotos',
        ])->findOrFail($id);
    }

    public function loadCompletionSummary($id): VisitChecklistCompletion
    {
        return VisitChecklistCompletion::with(['visit.sede', 'assignment.checklist'])->findOrFail($id);
    }

    public function loadCompletionFull($id): VisitChecklistCompletion
    {
        return VisitChecklistCompletion::with([
            'visit.empresa', 'visit.sede', 'visit.user',
            'assignment.checklist.sections',
            'assignment.inspection.asset.assetType',
        ])->findOrFail($id);
    }

    public function resolveTrainingForPdf(int $id): array
    {
        $visitTraining = VisitTraining::with([
            'visit.sede.empresa',
            'visit.user',
            'training.responsibleUser',
        ])->find($id);

        if ($visitTraining) {
            $training = $visitTraining;
            $training->status = 'completed';
            $training->sede = $visitTraining->visit->sede ?? null;
            $training->empresa = $visitTraining->visit->sede->empresa ?? null;
            $training->responsibleUser = $visitTraining->visit->user ?? ($visitTraining->training->responsibleUser ?? null);
            $training->trainer_name = $visitTraining->visit->user ? $visitTraining->visit->user->nombres : ($visitTraining->training->trainer_name ?? 'Instructor Externo');
            $training->date = $visitTraining->created_at;
            $training->duration = $visitTraining->duration_minutes;
            $training->attendances = $visitTraining->attendances()->orderBy('created_at')->get();
        } else {
            $training = Training::with([
                'empresa',
                'sede',
                'responsibleUser',
                'attendances',
            ])->findOrFail($id);
        }

        return [
            'training' => $training,
            'attendances' => $training->attendances()->orderBy('created_at')->get(),
            'branding' => BrandingService::forUser($training->responsibleUser),
        ];
    }

    public function loadInspectionForPdf(int $id): DynamicInspection
    {
        return DynamicInspection::with([
            'checklist.sections',
            'user',
            'sede.empresa',
            'answers.question',
            'asset.assetType',
        ])->findOrFail($id);
    }

    public function loadHallazgoForPdf(int $id): Hallazgo
    {
        return Hallazgo::with([
            'sede.empresa', 'user', 'seguimientos.user', 'fotos', 'origen',
        ])->findOrFail($id);
    }

    public function loadIncidenteForPdf(int $id): Incidente
    {
        return Incidente::with([
            'sede.empresa', 'user', 'fotos', 'hallazgo', 'visit',
        ])->findOrFail($id);
    }

    public function getPendingSignatureUsers(User $currentUser): Collection
    {
        $query = User::where('firma_status', 'pending');

        if (! $currentUser->isSuperAdmin()) {
            $query->where('consultora_id', $currentUser->consultora_id);
        }

        return $query->orderBy('apellidos')->get();
    }
}
