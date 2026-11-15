<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\AssetType;
use App\Models\DynamicChecklist;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnnualContractService
{
    public function __construct(private readonly AnnualComplianceCalculatorService $complianceCalculator) {}

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
        $complianceData = $this->complianceCalculator->calculateComplianceData($activeSede, $plans, $dynamicMonths, $start, $endDate);
        $efficiency = $this->complianceCalculator->calculateEfficiencyMetrics($complianceData, $plans);

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
}
