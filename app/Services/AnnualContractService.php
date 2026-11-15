<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\AssetType;
use App\Models\DynamicChecklist;
use App\Models\DynamicInspection;
use App\Models\Empresa;
use App\Models\MasterReport;
use App\Models\Sede;
use App\Models\Training;
use App\Models\User;
use App\Models\VisitTraining;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnnualContractService
{
    /**
     * Build the full PPD contract matrix and filter options for the index view.
     */
    public function getIndexData(User $user, Request $request): array
    {
        $availableSedes = $user->isAdmin()
            ? Sede::select('id', 'nombre_sede', 'empresa_id')
                ->when(! $user->isSuperAdmin(), fn ($q) => $q->whereHas('empresa', fn ($eq) => $eq->where('consultora_id', $user->consultora_id)))
                ->orderBy('nombre_sede')
                ->get()
            : $user->sedes()->select('sedes.id', 'sedes.nombre_sede', 'sedes.empresa_id')->orderBy('nombre_sede')->get();

        $companies = Empresa::select('id', 'nombre')
            ->whereIn('id', $availableSedes->pluck('empresa_id'))
            ->orderBy('nombre')
            ->get();

        $companyId = $request->query('company_id');
        $sedes = $companyId ? $availableSedes->where('empresa_id', $companyId)->values() : collect();
        $sedeId = $request->query('sede_id');

        $activeSede = $sedeId ? Sede::with(['empresa', 'users' => fn ($q) => $q->where('rol', 'tecnico')])->find($sedeId) : null;

        $contractStart = $request->query('contract_start', now()->format('Y-m'));
        $contractDuration = (int) $request->query('duration', 12);

        try {
            $start = Carbon::createFromFormat('Y-m', $contractStart)->startOfMonth();
        } catch (\Exception $e) {
            $start = now()->startOfMonth();
            $contractStart = $start->format('Y-m');
        }

        $dynamicMonths = [];
        for ($i = 0; $i < $contractDuration; $i++) {
            $current = $start->copy()->addMonths($i);
            $dynamicMonths[$current->format('Y-m')] = [
                'name' => $current->translatedFormat('F'),
                'short' => $current->translatedFormat('M'),
                'year' => $current->format('Y'),
                'month' => $current->month,
            ];
        }

        $plans = AnnualPlan::where('sede_id', $sedeId)
            ->when($request->category, fn ($q) => $q->where('category', $request->category))
            ->with('checklist')
            ->orderBy('category')
            ->get();

        $endDate = $start->copy()->addMonths($contractDuration - 1)->endOfMonth();
        $complianceData = $this->calculateComplianceData($activeSede, $plans, $dynamicMonths, $start, $endDate);
        $efficiency = $this->calculateEfficiencyMetrics($complianceData, $plans);

        return [
            'companies' => $companies,
            'sedes' => $sedes,
            'activeSede' => $activeSede,
            'plans' => $plans,
            'dynamicMonths' => $dynamicMonths,
            'complianceData' => $complianceData,
            'efficiency' => $efficiency,
            'filters' => [
                'company_id' => $companyId,
                'sede_id' => $sedeId,
                'contract_start' => $contractStart,
                'duration' => $contractDuration,
                'category' => $request->category,
            ],
            'checklistOptions' => $this->getChecklistOptions(),
        ];
    }

    /**
     * Create a new annual plan activity inside a transaction.
     */
    public function createPlan(array $data): void
    {
        $startDate = Carbon::createFromFormat('Y-m', $data['contract_start'])->startOfMonth();
        $endDate = $startDate->copy()->addMonths((int) $data['duration'] - 1)->endOfMonth();
        $scheduled = $this->buildScheduledMonths($startDate, (int) $data['duration'], $data['frequency']);

        DB::transaction(function () use ($data, $startDate, $endDate, $scheduled) {
            AnnualPlan::create([
                'sede_id' => $data['sede_id'],
                'period_start' => $startDate,
                'period_end' => $endDate,
                'year' => $startDate->year,
                'category' => $data['category'],
                'frequency' => $data['frequency'],
                'activity_name' => $data['activity_name'],
                'activity_code' => $data['activity_code'] ?? null,
                'checklist_id' => in_array($data['category'], ['Inspección', 'Capacitación']) ? ($data['checklist_id'] ?? null) : null,
                'scheduled_months' => $scheduled,
                'duration_minutes' => $data['category'] === 'Capacitación' ? ($data['duration_minutes'] ?: 15) : null,
                'modality' => $data['category'] === 'Capacitación' ? ($data['modality'] ?: 'presencial') : null,
                'multi_trainer' => $data['category'] === 'Capacitación' ? (bool) ($data['multi_trainer'] ?? false) : false,
            ]);
        });
    }

    /**
     * Toggle a month in an annual plan's schedule.
     */
    public function toggleMonth(int|string $id, string $monthValue): void
    {
        $plan = AnnualPlan::findOrFail($id);
        $scheduled = $plan->scheduled_months ?? [];

        if (in_array($monthValue, $scheduled)) {
            $scheduled = array_values(array_diff($scheduled, [$monthValue]));
            $dates = $plan->scheduled_dates ?? [];
            unset($dates[$monthValue]);
            $plan->scheduled_dates = $dates;
        } else {
            $scheduled[] = $monthValue;
            sort($scheduled);
        }

        $plan->scheduled_months = $scheduled;
        $plan->save();
    }

    /**
     * Set or clear a specific scheduled date for a month in an annual plan.
     */
    public function updateDate(int|string $id, string $monthValue, ?string $dateValue): void
    {
        $plan = AnnualPlan::findOrFail($id);
        $dates = $plan->scheduled_dates ?? [];

        if ($dateValue) {
            $dates[$monthValue] = $dateValue;
        } else {
            unset($dates[$monthValue]);
        }

        $plan->scheduled_dates = $dates;
        $plan->save();
    }

    /**
     * Update an annual plan activity and recalculate scheduled months if frequency changed.
     */
    public function updatePlan(int|string $id, array $data): void
    {
        $plan = AnnualPlan::findOrFail($id);
        $scheduled = $plan->scheduled_months ?? [];

        if ($data['frequency'] !== $plan->frequency) {
            $startDate = Carbon::parse($plan->period_start)->startOfMonth();
            $endDate = Carbon::parse($plan->period_end)->endOfMonth();
            $duration = (int) $startDate->diffInMonths($endDate) + 1;
            $scheduled = $this->buildScheduledMonths($startDate, $duration, $data['frequency']);
        }

        $plan->update([
            'category' => $data['category'],
            'activity_name' => $data['activity_name'],
            'activity_code' => $data['activity_code'] ?? null,
            'checklist_id' => in_array($data['category'], ['Inspección', 'Capacitación']) ? ($data['checklist_id'] ?? null) : null,
            'frequency' => $data['frequency'],
            'scheduled_months' => $scheduled,
            'duration_minutes' => $data['category'] === 'Capacitación' ? ($data['duration_minutes'] ?: 15) : null,
            'modality' => $data['category'] === 'Capacitación' ? ($data['modality'] ?: 'presencial') : null,
            'multi_trainer' => $data['category'] === 'Capacitación' ? (bool) ($data['multi_trainer'] ?? false) : false,
        ]);
    }

    /**
     * Delete an annual plan activity.
     */
    public function deletePlan(int|string $id): void
    {
        AnnualPlan::findOrFail($id)->delete();
    }

    private function buildScheduledMonths(Carbon $startDate, int $duration, string $frequency): array
    {
        if ($frequency === 'none') {
            return [];
        }

        $scheduled = [];
        for ($i = 0; $i < $duration; $i++) {
            $monthNum = $i + 1;
            $shouldCheck = $frequency === 'mensual'
                || ($frequency === 'bimestral' && $monthNum % 2 === 0)
                || ($frequency === 'trimestral' && $monthNum % 3 === 0);

            if ($shouldCheck) {
                $scheduled[] = $startDate->copy()->addMonths($i)->format('Y-m');
            }
        }

        return $scheduled;
    }

    private function getChecklistOptions()
    {
        $categoriesWithRealAssets = AssetType::withCount('assets')
            ->get()
            ->filter(fn ($at) => $at->assets_count > 0)
            ->pluck('category_id')
            ->unique()
            ->values()
            ->toArray();

        return DynamicChecklist::select('id', 'title', 'registry_code', 'category_id')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'registry_code' => $c->registry_code,
                'has_real_assets' => in_array($c->category_id, $categoriesWithRealAssets),
            ]);
    }

    private function calculateComplianceData(?Sede $activeSede, $plans, array $dynamicMonths, Carbon $startDate, Carbon $endDate): array
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
                        'count' => 0,
                        'passed' => 0,
                        'failed' => 0,
                        'findings' => 0,
                        'execution_date' => $inp->completed_at->format('d/m/Y'),
                        'history_groups' => [],
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
                        'total_checklists' => 0,
                        'passed' => 0,
                        'failed' => 0,
                    ];
                }

                $insLookup[$id][$m]['history_groups'][$groupKey]['total_checklists']++;
                if ($inp->result === 'Pass') {
                    $insLookup[$id][$m]['history_groups'][$groupKey]['passed']++;
                } else {
                    $insLookup[$id][$m]['history_groups'][$groupKey]['failed']++;
                }
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
                $trainLookup[$lowerTitle][$month] = [
                    'count' => 0,
                    'attendees' => 0,
                    'execution_date' => $executionDate,
                    'history' => [],
                ];
            }
            $trainLookup[$lowerTitle][$month]['count']++;
            $trainLookup[$lowerTitle][$month]['attendees'] += $attendeesCount;
            $trainLookup[$lowerTitle][$month]['history'][] = $historyItem;

            if ($checklistId) {
                if (! isset($trainChecklistLookup[$checklistId][$month])) {
                    $trainChecklistLookup[$checklistId][$month] = [
                        'count' => 0,
                        'attendees' => 0,
                        'execution_date' => $executionDate,
                        'history' => [],
                    ];
                }
                $trainChecklistLookup[$checklistId][$month]['count']++;
                $trainChecklistLookup[$checklistId][$month]['attendees'] += $attendeesCount;
                $trainChecklistLookup[$checklistId][$month]['history'][] = $historyItem;
            }
        };

        Training::with(['responsibleUser'])
            ->where('sede_id', $sedeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->withCount('attendances')
            ->get()
            ->each(function ($t) use ($appendEntry) {
                $date = Carbon::parse($t->date);
                $techName = $t->responsibleUser ? trim($t->responsibleUser->nombres.' '.$t->responsibleUser->apellidos) : 'N/D';
                $appendEntry(
                    $t->title,
                    $t->dynamic_checklist_id,
                    $date->format('Y-m'),
                    $date->format('d/m/Y'),
                    $t->attendances_count,
                    [
                        'date' => $date->toIso8601String(),
                        'technician_name' => $techName,
                        'asistentes' => $t->attendances_count,
                        'notes' => $t->notes ? strip_tags($t->notes) : null,
                    ]
                );
            });

        VisitTraining::whereHas('visit', fn ($q) => $q->where('sede_id', $sedeId)->whereBetween('fecha_visita', [$startDate, $endDate]))
            ->with(['visit.user', 'training', 'attendances'])
            ->get()
            ->each(function ($vt) use ($appendEntry) {
                $user = $vt->visit?->user;
                $techName = $user ? trim($user->nombres.' '.$user->apellidos) : 'N/D';
                $count = $vt->attendances->count();
                $appendEntry(
                    $vt->title,
                    $vt->training?->dynamic_checklist_id,
                    $vt->created_at->format('Y-m'),
                    $vt->created_at->format('d/m/Y'),
                    $count,
                    [
                        'date' => $vt->created_at->toIso8601String(),
                        'technician_name' => $techName,
                        'asistentes' => $count,
                        'notes' => $vt->notes ? strip_tags($vt->notes) : null,
                    ]
                );
            });

        return [$trainLookup, $trainChecklistLookup];
    }

    private function buildReportLookup(int $sedeId, Carbon $startDate, Carbon $endDate): array
    {
        $repLookup = [];

        MasterReport::with('tecnico')
            ->where('sede_id', $sedeId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->each(function ($r) use (&$repLookup) {
                $m = str_contains($r->periodo, '-') ? $r->periodo : $r->created_at->format('Y-m');
                if (! isset($repLookup[$m])) {
                    $repLookup[$m] = [
                        'count' => 0,
                        'execution_date' => $r->created_at->format('d/m/Y'),
                        'history' => [],
                    ];
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

    private function calculateEfficiencyMetrics(array $complianceData, $plans): array
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
}
