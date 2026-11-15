<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\DynamicInspection;
use App\Models\MasterReport;
use App\Models\Sede;
use App\Models\Training;
use App\Models\VisitTraining;
use Carbon\Carbon;

class AnnualComplianceCalculatorService
{
    public function calculateComplianceData(?Sede $activeSede, $plans, array $dynamicMonths, Carbon $startDate, Carbon $endDate): array
    {
        if (! $activeSede) {
            return [];
        }

        $sedeId = $activeSede->id;
        $insLookup = $this->buildInspectionLookup($sedeId, $startDate, $endDate);
        [$trainLookup, $trainChecklistLookup] = $this->buildTrainingLookups($sedeId, $startDate, $endDate);
        $repLookup = $this->buildReportLookup($sedeId, $startDate, $endDate);

        $data = [];
        $currentDate = now()->format('Y-m');

        foreach ($plans as $plan) {
            $data[$plan->id] = [];
            $scheduledMonths = $plan->scheduled_months ?? [];

            foreach ($dynamicMonths as $monthValue => $monthInfo) {
                if (! in_array($monthValue, $scheduledMonths)) {
                    $data[$plan->id][$monthValue] = ['status' => 'none', 'count' => 0];
                    continue;
                }

                $found = $this->findPlanExecution($plan, $monthValue, $insLookup, $trainLookup, $trainChecklistLookup, $repLookup);

                if ($found) {
                    $details = array_merge(
                        ['passed' => 0, 'failed' => 0, 'findings' => 0, 'attendees' => 0, 'execution_date' => null, 'history' => []],
                        $found
                    );
                    $data[$plan->id][$monthValue] = ['status' => 'completed', 'count' => $found['count'], 'details' => $details];
                } else {
                    $specificDate = ($plan->scheduled_dates ?? [])[$monthValue] ?? null;
                    $isOverdue = $specificDate ? Carbon::parse($specificDate)->isPast() : ($monthValue < $currentDate);
                    $data[$plan->id][$monthValue] = ['status' => $isOverdue ? 'overdue' : 'pending', 'count' => 0];
                }
            }
        }

        return $data;
    }

    public function calculateEfficiencyMetrics(array $complianceData, $plans): array
    {
        $stats = [
            'global' => 0,
            'inspeccion' => 0,
            'capacitacion' => 0,
            'informe' => 0,
            'counts' => ['total' => 0, 'completed' => 0, 'overdue' => 0, 'pending' => 0],
        ];

        foreach ($complianceData as $planId => $months) {
            if (! $plans->firstWhere('id', $planId)) {
                continue;
            }
            foreach ($months as $data) {
                if ($data['status'] === 'none') {
                    continue;
                }
                $stats['counts']['total']++;
                if (isset($stats['counts'][$data['status']])) {
                    $stats['counts'][$data['status']]++;
                }
            }
        }

        $stats['global'] = $stats['counts']['total'] > 0
            ? round(($stats['counts']['completed'] / $stats['counts']['total']) * 100)
            : 0;

        return $stats;
    }

    private function findPlanExecution(
        AnnualPlan $plan,
        string $monthValue,
        array $insLookup,
        array $trainLookup,
        array $trainChecklistLookup,
        array $repLookup
    ): ?array {
        if ($plan->category === 'Inspección' && $plan->checklist_id) {
            return $insLookup[$plan->checklist_id][$monthValue] ?? null;
        }
        if ($plan->category === 'Capacitación') {
            return $plan->checklist_id
                ? ($trainChecklistLookup[$plan->checklist_id][$monthValue] ?? null)
                : ($trainLookup[mb_strtolower($plan->activity_name)][$monthValue] ?? null);
        }
        if ($plan->category === 'Informe') {
            return $repLookup[$monthValue] ?? null;
        }

        return null;
    }

    private function buildInspectionLookup(int $sedeId, Carbon $startDate, Carbon $endDate): array
    {
        $insLookup = [];

        DynamicInspection::with('user')
            ->where('sede_id', $sedeId)
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get()
            ->each(function ($inp) use (&$insLookup) {
                $m = $inp->completed_at->format('Y-m');
                $id = $inp->dynamic_checklist_id;

                if (! isset($insLookup[$id][$m])) {
                    $insLookup[$id][$m] = [
                        'count' => 0, 'passed' => 0, 'failed' => 0, 'findings' => 0,
                        'execution_date' => $inp->completed_at->format('d/m/Y'), 'history_groups' => [],
                    ];
                }

                $insLookup[$id][$m]['count']++;
                $inp->result === 'Pass' ? $insLookup[$id][$m]['passed']++ : $insLookup[$id][$m]['failed']++;

                $techName = $inp->user ? trim($inp->user->nombres.' '.$inp->user->apellidos) : 'N/D';
                $groupKey = $inp->completed_at->format('Y-m-d').'_'.($inp->user_id ?? 0);

                if (! isset($insLookup[$id][$m]['history_groups'][$groupKey])) {
                    $insLookup[$id][$m]['history_groups'][$groupKey] = [
                        'date' => $inp->completed_at->toIso8601String(),
                        'technician_name' => $techName,
                        'total_checklists' => 0, 'passed' => 0, 'failed' => 0,
                    ];
                }

                $insLookup[$id][$m]['history_groups'][$groupKey]['total_checklists']++;
                $inp->result === 'Pass'
                    ? $insLookup[$id][$m]['history_groups'][$groupKey]['passed']++
                    : $insLookup[$id][$m]['history_groups'][$groupKey]['failed']++;
            });

        foreach ($insLookup as $id => $months) {
            foreach ($months as $m => $data) {
                $insLookup[$id][$m]['history'] = array_values($data['history_groups'] ?? []);
                unset($insLookup[$id][$m]['history_groups']);
            }
        }

        return $insLookup;
    }

    private function buildTrainingLookups(int $sedeId, Carbon $startDate, Carbon $endDate): array
    {
        $trainLookup = [];
        $trainChecklistLookup = [];

        $appendEntry = function (string $title, ?int $checklistId, string $month, string $executionDate, int $attendeesCount, array $historyItem) use (&$trainLookup, &$trainChecklistLookup) {
            $lowerTitle = mb_strtolower($title);
            if (! isset($trainLookup[$lowerTitle][$month])) {
                $trainLookup[$lowerTitle][$month] = ['count' => 0, 'attendees' => 0, 'execution_date' => $executionDate, 'history' => []];
            }
            $trainLookup[$lowerTitle][$month]['count']++;
            $trainLookup[$lowerTitle][$month]['attendees'] += $attendeesCount;
            $trainLookup[$lowerTitle][$month]['history'][] = $historyItem;

            if ($checklistId) {
                if (! isset($trainChecklistLookup[$checklistId][$month])) {
                    $trainChecklistLookup[$checklistId][$month] = ['count' => 0, 'attendees' => 0, 'execution_date' => $executionDate, 'history' => []];
                }
                $trainChecklistLookup[$checklistId][$month]['count']++;
                $trainChecklistLookup[$checklistId][$month]['attendees'] += $attendeesCount;
                $trainChecklistLookup[$checklistId][$month]['history'][] = $historyItem;
            }
        };

        Training::with(['responsibleUser'])->where('sede_id', $sedeId)->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'completed')->withCount('attendances')->get()
            ->each(function ($t) use ($appendEntry) {
                $date = Carbon::parse($t->date);
                $techName = $t->responsibleUser ? trim($t->responsibleUser->nombres.' '.$t->responsibleUser->apellidos) : 'N/D';
                $appendEntry($t->title, $t->dynamic_checklist_id, $date->format('Y-m'), $date->format('d/m/Y'), $t->attendances_count, [
                    'date' => $date->toIso8601String(), 'technician_name' => $techName,
                    'asistentes' => $t->attendances_count, 'notes' => $t->notes ? strip_tags($t->notes) : null,
                ]);
            });

        VisitTraining::whereHas('visit', fn ($q) => $q->where('sede_id', $sedeId)->whereBetween('fecha_visita', [$startDate, $endDate]))
            ->with(['visit.user', 'training', 'attendances'])->get()
            ->each(function ($vt) use ($appendEntry) {
                $user = $vt->visit?->user;
                $techName = $user ? trim($user->nombres.' '.$user->apellidos) : 'N/D';
                $count = $vt->attendances->count();
                $appendEntry($vt->title, $vt->training?->dynamic_checklist_id, $vt->created_at->format('Y-m'), $vt->created_at->format('d/m/Y'), $count, [
                    'date' => $vt->created_at->toIso8601String(), 'technician_name' => $techName,
                    'asistentes' => $count, 'notes' => $vt->notes ? strip_tags($vt->notes) : null,
                ]);
            });

        return [$trainLookup, $trainChecklistLookup];
    }

    private function buildReportLookup(int $sedeId, Carbon $startDate, Carbon $endDate): array
    {
        $repLookup = [];

        MasterReport::with('tecnico')->where('sede_id', $sedeId)->whereBetween('created_at', [$startDate, $endDate])->get()
            ->each(function ($r) use (&$repLookup) {
                $m = str_contains($r->periodo, '-') ? $r->periodo : $r->created_at->format('Y-m');
                if (! isset($repLookup[$m])) {
                    $repLookup[$m] = ['count' => 0, 'execution_date' => $r->created_at->format('d/m/Y'), 'history' => []];
                }
                $repLookup[$m]['count']++;
                $techName = $r->tecnico ? trim($r->tecnico->nombres.' '.$r->tecnico->apellidos) : 'N/D';
                $repLookup[$m]['history'][] = [
                    'date' => $r->created_at->toIso8601String(),
                    'technician_name' => $techName,
                    'observaciones' => $r->observaciones_visitas ?: 'Sin observaciones',
                    'notes' => $r->resumen_ejecutivo ? strip_tags($r->resumen_ejecutivo) : null,
                ];
            });

        return $repLookup;
    }
}
