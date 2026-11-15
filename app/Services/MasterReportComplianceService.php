<?php

namespace App\Services;

use App\Models\Sede;
use App\Models\VisitCertificate;
use Carbon\Carbon;

class MasterReportComplianceService
{
    public function collectAdhocActivities(Sede $sede, Carbon $startDate, Carbon $endDate, array $data)
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

    public function calculateAnnualPlanStatus(Sede $sede, Carbon $startDate, Carbon $endDate, array $data): array
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
            return [Carbon::parse($completion->completed_at)->format('d/m/Y'), $completion->visitCertificate?->user?->nombres ?? 'N/A'];
        }
        if ($category === 'Capacitación') {
            return [Carbon::parse($completion->date ?? $completion->created_at)->format('d/m/Y'), $completion->visitCertificate?->user?->nombres ?? 'N/A'];
        }
        if ($completion instanceof VisitCertificate) {
            return [$completion->visit_date->format('d/m/Y'), $completion->user?->nombres ?? 'N/A'];
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
