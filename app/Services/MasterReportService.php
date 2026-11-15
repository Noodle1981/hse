<?php

namespace App\Services;

use App\Models\MasterReport;
use App\Models\Sede;
use App\Models\User;
use App\Models\VisitCertificate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MasterReportService
{
    /**
     * Build paginated master report listing scoped by user role and filters.
     */
    public function getIndexData(User $user, Request $request): array
    {
        $query = MasterReport::with(['sede', 'tecnico', 'gestor'])->latest();

        if ($user->isTechnician()) {
            $query->where('user_id', $user->id);
        } elseif (! $user->isSuperAdmin()) {
            $query->whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $user->consultora_id));
        }

        if ($request->status) {
            $query->where('estado', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('sede', fn ($sq) => $sq->where('nombre_sede', 'like', '%'.$request->search.'%'))
                    ->orWhere('titulo', 'like', '%'.$request->search.'%');
            });
        }

        return [
            'reports' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'status']),
        ];
    }

    /**
     * Build create form options for master reports.
     */
    public function getCreateData(User $user, Request $request): array
    {
        $consultoraId = $user->isSuperAdmin() ? null : $user->consultora_id;

        $sedes = $user->isTechnician()
            ? $user->sedes()->with('empresa')->get()
            : Sede::with('empresa')
                ->when($consultoraId, fn ($q) => $q->whereHas('empresa', fn ($eq) => $eq->where('consultora_id', $consultoraId)))
                ->orderBy('nombre_sede')
                ->get();

        $tecnicos = User::where('rol', User::ROLE_TECNICO)
            ->when($consultoraId, fn ($q) => $q->where('consultora_id', $consultoraId))
            ->get();

        return [
            'sedes' => $sedes,
            'tecnicos' => $tecnicos,
            'initial_sede_id' => $request->sede_id,
            'initial_periodo' => $request->periodo,
            'initial_user_id' => $request->user_id ?? ($user->isTechnician() ? (string) $user->id : ''),
        ];
    }

    /**
     * Create a new master report record.
     */
    public function createReport(User $user, array $validatedData): MasterReport
    {
        return MasterReport::create([
            ...$validatedData,
            'solicitado_por' => $user->id,
            'estado' => $user->isTechnician() ? 'en_redaccion' : 'solicitado',
        ]);
    }

    /**
     * Load relations and aggregate period data for wizard or PDF preview.
     */
    public function getWizardData(MasterReport $masterReport): array
    {
        $masterReport->load(['sede.empresa', 'tecnico', 'gestor']);

        return [
            'report' => $masterReport,
            'periodData' => $this->aggregatePeriodData($masterReport),
        ];
    }

    /**
     * Apply state transition updates on a master report and return redirect message if transitioned.
     */
    public function handleUpdateTransition(User $user, MasterReport $masterReport, Request $request): ?string
    {
        if ($user->isGestor() || $user->isSuperAdmin()) {
            if ($request->action === 'approve') {
                $masterReport->update([
                    'estado' => 'aprobado',
                    'observacion_revision' => null,
                ]);

                return 'Informe maestro aprobado exitosamente.';
            }

            if ($request->action === 'observe') {
                $masterReport->update([
                    'estado' => 'en_redaccion',
                    'observacion_revision' => $request->observacion_revision,
                ]);

                return 'Informe maestro observado y devuelto al técnico.';
            }
        }

        if ($request->finalize) {
            $masterReport->update(['estado' => 'finalizado']);

            return 'Informe finalizado exitosamente.';
        }

        return null;
    }

    /**
     * Aggregate visits, checklists, trainings, findings, incidents, and annual plan status for the report period.
     */
    public function aggregatePeriodData(MasterReport $report): array
    {
        [$startDate, $endDate] = $this->resolvePeriodRange($report->periodo, $report->frecuencia ?? 'mensual');

        $visits = $report->sede->visitCertificates()
            ->with([
                'checklistCompletions.assignment.checklist',
                'checklistCompletions.assignment.asset.assetType',
                'checklistCompletions.assignment.inspection.asset.assetType',
                'trainings.attendances',
                'hallazgos.fotos',
                'incidentes.fotos',
                'user',
            ])
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->whereIn('status', ['completed', 'signed'])
            ->get();

        $data = [
            'visits' => $visits,
            'checklists' => $visits->flatMap->checklistCompletions,
            'trainings' => $visits->flatMap->trainings,
            'hallazgos' => $visits->flatMap->hallazgos,
            'incidentes' => $visits->flatMap->incidentes,
        ];

        $data['annual_plan_status'] = $this->calculateAnnualPlanStatus($report->sede, $startDate, $endDate, $data);
        $data['adhoc_activities'] = $this->collectAdhocActivities($report->sede, $startDate, $endDate, $data);

        return $data;
    }

    private function resolvePeriodRange(string $period, string $freq): array
    {
        if ($freq === 'trimestral') {
            $q = (int) substr($period, -1);
            $y = (int) substr($period, 0, 4);
            $startMonth = ($q - 1) * 3 + 1;

            return [
                Carbon::create($y, $startMonth, 1)->startOfMonth(),
                Carbon::create($y, $startMonth + 2, 1)->endOfMonth(),
            ];
        }

        if ($freq === 'semestral') {
            $s = (int) substr($period, -1);
            $y = (int) substr($period, 0, 4);
            $startMonth = ($s - 1) * 6 + 1;

            return [
                Carbon::create($y, $startMonth, 1)->startOfMonth(),
                Carbon::create($y, $startMonth + 5, 1)->endOfMonth(),
            ];
        }

        if ($freq === 'anual') {
            $y = (int) substr($period, 0, 4);

            return [
                Carbon::create($y, 1, 1)->startOfMonth(),
                Carbon::create($y, 12, 31)->endOfMonth(),
            ];
        }

        try {
            return [
                Carbon::parse($period.'-01')->startOfMonth(),
                Carbon::parse($period.'-01')->endOfMonth(),
            ];
        } catch (\Exception $e) {
            return [now()->startOfMonth(), now()->endOfMonth()];
        }
    }

    private function collectAdhocActivities(Sede $sede, Carbon $startDate, Carbon $endDate, array $data)
    {
        $satisfiedCheckIds = $data['annual_plan_status']['satisfied_checklist_ids'] ?? collect();
        $satisfiedTrainIds = $data['annual_plan_status']['satisfied_training_ids'] ?? collect();

        $plans = $sede->annualPlans()
            ->where('period_start', '<=', $endDate)
            ->where('period_end', '>=', $startDate)
            ->get();

        $adhoc = collect();

        $data['checklists']->reject(fn ($c) => $satisfiedCheckIds->contains($c->id))
            ->each(function ($c) use ($adhoc, $plans) {
                $isProgrammed = $plans->contains(function ($plan) use ($c) {
                    if ($plan->category !== 'Inspección') {
                        return false;
                    }
                    if ($plan->checklist_id && $plan->checklist_id == ($c->assignment?->dynamic_checklist_id ?? null)) {
                        return true;
                    }
                    if ($plan->activity_code && ($c->assignment?->checklist?->registry_code ?? '') === $plan->activity_code) {
                        return true;
                    }

                    return $plan->activity_name && mb_strtolower($plan->activity_name) === mb_strtolower($c->assignment?->checklist?->title ?? '');
                });

                if (! $isProgrammed) {
                    $adhoc->push([
                        'activity' => $c->assignment?->checklist?->title ?? 'Punto de Control',
                        'category' => 'Inspección',
                        'description' => $c->assignment?->checklist?->description ?? null,
                        'date' => Carbon::parse($c->completed_at)->format('d/m/Y'),
                        'technician' => $c->visitCertificate->technician_name ?? 'N/A',
                    ]);
                }
            });

        $data['trainings']->reject(fn ($t) => $satisfiedTrainIds->contains($t->id))
            ->each(function ($t) use ($adhoc, $plans) {
                $isProgrammed = $plans->contains(function ($plan) use ($t) {
                    if ($plan->category !== 'Capacitación') {
                        return false;
                    }
                    if ($plan->activity_code && str_contains(mb_strtolower($t->title), mb_strtolower($plan->activity_code))) {
                        return true;
                    }
                    if ($plan->activity_name) {
                        $planTitle = mb_strtolower($plan->activity_name);
                        $trainingTitle = mb_strtolower($t->title);

                        return str_contains($trainingTitle, $planTitle) || str_contains($planTitle, $trainingTitle);
                    }

                    return false;
                });

                if (! $isProgrammed) {
                    $adhoc->push([
                        'activity' => $t->title,
                        'category' => 'Capacitación',
                        'description' => $t->description,
                        'date' => Carbon::parse($t->date ?? $t->created_at)->format('d/m/Y'),
                        'technician' => $t->trainer_name ?? 'N/A',
                    ]);
                }
            });

        return $adhoc;
    }

    private function calculateAnnualPlanStatus(Sede $sede, Carbon $startDate, Carbon $endDate, array $data): array
    {
        $plans = $sede->annualPlans()
            ->with('checklist')
            ->where('period_start', '<=', $endDate)
            ->where('period_end', '>=', $startDate)
            ->get();

        if ($plans->isEmpty()) {
            return [
                'percentage' => 0,
                'programmed' => collect(),
                'total_targets' => 0,
                'achieved' => 0,
                'satisfied_checklist_ids' => collect(),
                'satisfied_training_ids' => collect(),
            ];
        }

        $monthsToEval = [];
        $curr = $startDate->copy();
        while ($curr->lte($endDate)) {
            $monthsToEval[] = $curr->format('Y-m');
            $curr->addMonth();
        }

        $programmed = collect();
        $totalTargets = 0;
        $achievedTargets = 0;
        $satisfiedChecklistIds = collect();
        $satisfiedTrainingIds = collect();

        foreach ($plans as $plan) {
            foreach ($monthsToEval as $m) {
                if (! is_array($plan->scheduled_months) || ! in_array($m, $plan->scheduled_months)) {
                    continue;
                }

                $isReport = ($plan->category === 'Informe');
                if (! $isReport) {
                    $totalTargets++;
                }

                $dt = Carbon::parse($m.'-01');
                $completion = $this->checkActivityCompletion($plan, $dt->month, $dt->year, $data);

                if ($completion) {
                    if (! $isReport) {
                        $achievedTargets++;
                    }
                    if ($plan->category === 'Inspección' && isset($completion->id)) {
                        $satisfiedChecklistIds->push($completion->id);
                    }
                    if ($plan->category === 'Capacitación' && isset($completion->id)) {
                        $satisfiedTrainingIds->push($completion->id);
                    }
                }

                [$executedAt, $technician] = $this->resolveExecutionMeta($plan->category, $completion);

                $programmed->push([
                    'activity' => $plan->activity_name,
                    'code' => $plan->activity_code,
                    'category' => $plan->category,
                    'description' => $plan->checklist->description ?? null,
                    'month' => Carbon::parse($m.'-01')->translatedFormat('F'),
                    'status' => $completion ? 'completed' : 'pending',
                    'executed_at' => $executedAt,
                    'technician' => $technician,
                ]);
            }
        }

        return [
            'percentage' => $totalTargets > 0 ? round(($achievedTargets / $totalTargets) * 100) : 100,
            'programmed' => $programmed,
            'total_targets' => $totalTargets,
            'achieved' => $achievedTargets,
            'satisfied_checklist_ids' => $satisfiedChecklistIds,
            'satisfied_training_ids' => $satisfiedTrainingIds,
        ];
    }

    private function resolveExecutionMeta(string $category, mixed $completion): array
    {
        if (! $completion || ! is_object($completion)) {
            return [null, null];
        }

        if ($category === 'Inspección' && isset($completion->completed_at)) {
            return [
                Carbon::parse($completion->completed_at)->format('d/m/Y'),
                $completion->visitCertificate?->user?->nombres ?? 'N/A',
            ];
        }

        if ($category === 'Capacitación') {
            return [
                Carbon::parse($completion->date ?? $completion->created_at)->format('d/m/Y'),
                $completion->visitCertificate?->user?->nombres ?? 'N/A',
            ];
        }

        if ($completion instanceof VisitCertificate) {
            return [
                $completion->visit_date->format('d/m/Y'),
                $completion->user?->nombres ?? 'N/A',
            ];
        }

        return [null, null];
    }

    private function checkActivityCompletion(mixed $plan, int $month, int $year, array $data): mixed
    {
        if (stripos($plan->activity_name, 'Visita') !== false) {
            return $data['visits']->first(fn ($v) => $v->visit_date->month == $month && $v->visit_date->year == $year);
        }

        if ($plan->activity_code) {
            if ($plan->category === 'Inspección') {
                $found = $data['checklists']->first(function ($c) use ($plan, $month, $year) {
                    $date = Carbon::parse($c->completed_at);

                    return $date->month == $month && $date->year == $year
                        && (($c->assignment?->checklist?->registry_code ?? '') === $plan->activity_code);
                });
                if ($found) {
                    return $found;
                }
            }

            if ($plan->category === 'Capacitación') {
                $found = $data['trainings']->first(function ($t) use ($plan, $month, $year) {
                    $date = Carbon::parse($t->date ?? $t->created_at);

                    return $date->month == $month && $date->year == $year
                        && str_contains(mb_strtolower($t->title), mb_strtolower($plan->activity_code));
                });
                if ($found) {
                    return $found;
                }
            }
        }

        if ($plan->category === 'Inspección' && $plan->checklist_id) {
            return $data['checklists']->first(function ($c) use ($plan, $month, $year) {
                $date = Carbon::parse($c->completed_at);

                return ($c->assignment?->dynamic_checklist_id ?? null) == $plan->checklist_id
                    && $date->month == $month && $date->year == $year;
            });
        }

        if ($plan->category === 'Capacitación') {
            return $data['trainings']->first(function ($t) use ($plan, $month, $year) {
                $date = Carbon::parse($t->date ?? $t->created_at);
                $planTitle = mb_strtolower($plan->activity_name);
                $trainingTitle = mb_strtolower($t->title);

                return (str_contains($trainingTitle, $planTitle) || str_contains($planTitle, $trainingTitle))
                    && $date->month == $month && $date->year == $year;
            });
        }

        return false;
    }
}
