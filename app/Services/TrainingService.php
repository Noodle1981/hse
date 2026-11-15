<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\DynamicChecklist;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\Training;
use App\Models\TrainingAttendance;
use App\Models\User;
use App\Models\VisitCertificate;
use App\Models\VisitTraining;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrainingService
{
    /**
     * Build listing data for training templates and visit training history.
     */
    public function getIndexData(User $user, Request $request): array
    {
        $consultoraId = $user->isSuperAdmin() ? null : $user->consultora_id;

        $templates = Training::where('is_template', true)
            ->when($consultoraId, fn ($q) => $q->whereHas('empresa', fn ($eq) => $eq->where('consultora_id', $consultoraId)))
            ->with(['empresa', 'creator'])
            ->latest()
            ->get();

        $history = VisitTraining::query()
            ->with(['visit.sede.empresa', 'visit.user', 'training.responsibleUser', 'training.creator'])
            ->when($consultoraId, function ($query) use ($consultoraId) {
                $query->whereHas('visit.sede.empresa', fn ($q) => $q->where('consultora_id', $consultoraId));
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', '%'.$request->search.'%')
                        ->orWhere('description', 'like', '%'.$request->search.'%');
                });
            })
            ->when($request->empresaFilter, function ($query) use ($request) {
                $query->whereHas('visit.sede', fn ($q) => $q->where('empresa_id', $request->empresaFilter));
            })
            ->when($request->sedeFilter, function ($query) use ($request) {
                $query->whereHas('visit', fn ($q) => $q->where('sede_id', $request->sedeFilter));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $empresas = Empresa::when($consultoraId, fn ($q) => $q->where('consultora_id', $consultoraId))->get();
        $sedes = $request->empresaFilter
            ? Sede::where('empresa_id', $request->empresaFilter)->get()
            : [];

        return [
            'templates' => $templates,
            'history' => $history,
            'empresas' => $empresas,
            'sedes' => $sedes,
            'filters' => $request->only(['search', 'empresaFilter', 'sedeFilter']),
        ];
    }

    /**
     * Build form data for creating a training template.
     */
    public function getCreateData(User $user): array
    {
        $consultoraId = $user->isSuperAdmin() ? null : $user->consultora_id;

        return [
            'sedes' => Sede::with('empresa')
                ->when($consultoraId, fn ($q) => $q->whereHas('empresa', fn ($eq) => $eq->where('consultora_id', $consultoraId)))
                ->get(),
            'empresas' => Empresa::when($consultoraId, fn ($q) => $q->where('consultora_id', $consultoraId))->get(),
            'tecnicos' => User::where('rol', 'tecnico')
                ->when($consultoraId, fn ($q) => $q->where('consultora_id', $consultoraId))
                ->get(),
            'checklists' => DynamicChecklist::active()->get(),
        ];
    }

    /**
     * Create a new training template or scheduled training.
     */
    public function createTraining(User $user, array $validated, bool $isTemplate): Training
    {
        return Training::create(array_merge($validated, [
            'created_by_user_id' => $user->id,
            'is_template' => $isTemplate,
        ]));
    }

    /**
     * Start a training session within an active visit, inheriting annual plan settings when available.
     */
    public function startInVisit(VisitCertificate $visit, array $data): VisitTraining
    {
        $training = ! empty($data['training_id']) ? Training::find($data['training_id']) : null;

        $durationMinutes = 15;
        if ($training && $training->dynamic_checklist_id) {
            $plan = AnnualPlan::where('sede_id', $visit->sede_id)
                ->where('checklist_id', $training->dynamic_checklist_id)
                ->first();

            if ($plan && $plan->duration_minutes) {
                $durationMinutes = $plan->duration_minutes;
            }
        }

        return VisitTraining::create([
            'visit_certificate_id' => $visit->id,
            'training_id' => $data['training_id'] ?? null,
            'is_adhoc' => empty($data['training_id']),
            'title' => $training ? $training->title : ($data['title'] ?? ''),
            'description' => $training ? $training->description : ($data['description'] ?? null),
            'duration_minutes' => $training
                ? ($training->duration ?: $durationMinutes)
                : ($data['duration'] ?? $durationMinutes),
            'attendees' => [],
            'notes' => $training ? null : ($data['notes'] ?? null),
        ]);
    }

    /**
     * Register an attendee and signature in a training session.
     */
    public function addAttendee(VisitTraining $visitTraining, array $data): TrainingAttendance
    {
        return TrainingAttendance::create([
            'training_id' => $visitTraining->training_id,
            'visit_training_id' => $visitTraining->id,
            'employee_name' => $data['name'],
            'dni' => $data['dni'] ?? null,
            'signature' => $data['signature'],
        ]);
    }

    /**
     * Remove an attendee by positional index from a training session.
     */
    public function removeAttendee(VisitTraining $visitTraining, int $index): void
    {
        $attendance = $visitTraining->attendances()->orderBy('id')->skip($index)->first();
        if ($attendance) {
            $attendance->delete();
        }
    }

    /**
     * Finalize a training session, save external instructor signature, and close training-only visits.
     */
    public function finalizeSession(VisitTraining $visitTraining, array $data): bool
    {
        $visitTraining->update([
            'duration_minutes' => $data['duration_minutes'],
        ]);

        $isExternal = (bool) ($data['is_external_instructor'] ?? false);
        $signaturePath = $isExternal && ! empty($data['instructor_signature'])
            ? $this->storeBase64Signature($data['instructor_signature'])
            : null;

        if ($visitTraining->training) {
            $updateData = [
                'status' => 'completed',
                'duration' => $data['duration_minutes'],
            ];
            if ($isExternal && ! empty($data['trainer_name'])) {
                $updateData['trainer_name'] = $data['trainer_name'];
            }
            $visitTraining->training->update($updateData);
        }

        $visit = $visitTraining->visit;
        if ($visit) {
            if ($isExternal && $signaturePath) {
                $visit->instructor_signature = $signaturePath;
                $visit->instructor_signature_date = now();
            } elseif (! $isExternal) {
                $visit->instructor_signature = null;
                $visit->instructor_signature_date = null;
            }
            $visit->responsible_name = $data['responsible_name'];
            $visit->signature = $data['responsible_signature'];
            $visit->save();
        }

        if ($visit && $visit->visit_type === 'training') {
            $visit->update([
                'end_time' => now(),
                'status' => 'signed',
                'general_observations' => 'Capacitación dictada con éxito. Total asistentes: '.count($visitTraining->attendees ?? []),
                'notes' => 'Visita de capacitación finalizada automáticamente al completar la charla: '.$visitTraining->title,
                'responsible_name' => $data['responsible_name'],
                'signature' => $data['responsible_signature'],
            ]);

            return true;
        }

        return false;
    }

    /**
     * Build edit form data for a training template.
     */
    public function getEditData(User $user, Training $training): array
    {
        $consultoraId = $user->isSuperAdmin() ? null : $user->consultora_id;

        return [
            'training' => $training,
            'sedes' => Sede::with('empresa')
                ->when($consultoraId, fn ($q) => $q->whereHas('empresa', fn ($eq) => $eq->where('consultora_id', $consultoraId)))
                ->get(),
        ];
    }

    /**
     * Find a visit training session with relations for attendance view.
     */
    public function getAttendanceSession(int|string $id): VisitTraining
    {
        return VisitTraining::with(['visit.sede.empresa', 'training'])->findOrFail($id);
    }

    private function storeBase64Signature(string $sigData): ?string
    {
        $type = 'png';
        if (preg_match('/^data:image\/(\w+);base64,/', $sigData, $m)) {
            $sigData = substr($sigData, strpos($sigData, ',') + 1);
            $type = strtolower($m[1]);
        }
        $decoded = base64_decode($sigData);
        if ($decoded === false) {
            return null;
        }
        $filename = 'signatures/instructor_'.uniqid().'.'.$type;
        Storage::disk('public')->put($filename, $decoded);

        return $filename;
    }
}
