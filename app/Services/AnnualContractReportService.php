<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\DynamicInspection;
use App\Models\MasterReport;
use App\Models\Sede;
use App\Models\Training;
use App\Models\VisitTraining;
use Carbon\Carbon;

class AnnualContractReportService
{
    public function buildAnnualReportData($sedeId, $year): array
    {
        $sede = Sede::with('empresa')->findOrFail($sedeId);

        $startDate = Carbon::create($year, 1, 1)->startOfDay();
        $endDate = Carbon::create($year, 12, 31)->endOfDay();

        $plans = AnnualPlan::where('sede_id', $sedeId)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('period_start', [$startDate, $endDate])
                    ->orWhereBetween('period_end', [$startDate, $endDate])
                    ->orWhere(function ($sub) use ($startDate, $endDate) {
                        $sub->where('period_start', '<=', $startDate)
                            ->where('period_end', '>=', $endDate);
                    });
            })
            ->with('checklist')
            ->orderBy('category')
            ->get();

        $months = [
            'enero' => 1, 'febrero' => 2, 'marzo' => 3, 'abril' => 4,
            'mayo' => 5, 'junio' => 6, 'julio' => 7, 'agosto' => 8,
            'septiembre' => 9, 'octubre' => 10, 'noviembre' => 11, 'diciembre' => 12,
        ];

        $insLookup = DynamicInspection::where('sede_id', $sedeId)
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get()
            ->groupBy(fn ($i) => $i->dynamic_checklist_id.'_'.$i->completed_at->format('Y-m'));

        $trainLookup = [];
        $trainChecklistLookup = [];

        Training::where('sede_id', $sedeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get()
            ->each(function ($t) use (&$trainLookup, &$trainChecklistLookup) {
                $m = $t->date->format('Y-m');
                $trainLookup[mb_strtolower($t->title)][$m] = true;
                if ($t->dynamic_checklist_id) {
                    $trainChecklistLookup[$t->dynamic_checklist_id][$m] = true;
                }
            });

        VisitTraining::whereHas('visit', fn ($q) => $q->where('sede_id', $sedeId)->whereBetween('fecha_visita', [$startDate, $endDate]))
            ->with('training')
            ->get()
            ->each(function ($vt) use (&$trainLookup, &$trainChecklistLookup) {
                $m = $vt->created_at->format('Y-m');
                $trainLookup[mb_strtolower($vt->title)][$m] = true;
                if ($vt->training?->dynamic_checklist_id) {
                    $trainChecklistLookup[$vt->training->dynamic_checklist_id][$m] = true;
                }
            });

        $repLookup = MasterReport::where('sede_id', $sedeId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(fn ($r) => str_contains($r->periodo, '-') ? $r->periodo : $r->created_at->format('Y-m'));

        $reportData = [];
        foreach ($plans as $plan) {
            $monthData = [];
            foreach ($months as $name => $num) {
                $monthStr = $year.'-'.str_pad($num, 2, '0', STR_PAD_LEFT);

                if (! in_array($monthStr, $plan->scheduled_months ?? [])) {
                    $monthData[$name] = ['status' => 'none', 'count' => 0];
                    continue;
                }

                $count = 0;
                if ($plan->category === 'Inspección' && $plan->checklist_id) {
                    $count = isset($insLookup[$plan->checklist_id.'_'.$monthStr]) ? $insLookup[$plan->checklist_id.'_'.$monthStr]->count() : 0;
                } elseif ($plan->category === 'Capacitación') {
                    $count = $plan->checklist_id
                        ? (isset($trainChecklistLookup[$plan->checklist_id][$monthStr]) ? 1 : 0)
                        : (isset($trainLookup[mb_strtolower($plan->activity_name)][$monthStr]) ? 1 : 0);
                } elseif ($plan->category === 'Informe') {
                    $count = isset($repLookup[$monthStr]) ? $repLookup[$monthStr]->count() : 0;
                }

                if ($count > 0) {
                    $monthData[$name] = ['status' => 'completed', 'count' => $count];
                } elseif ($plan->status === 'suspended') {
                    $monthData[$name] = ['status' => 'suspended', 'count' => 0];
                } elseif ($plan->status === 'cancelled') {
                    $monthData[$name] = ['status' => 'cancelled', 'count' => 0];
                } else {
                    $currentDate = now()->format('Y-m');
                    $monthData[$name] = ['status' => $monthStr < $currentDate ? 'overdue' : 'pending', 'count' => 0];
                }
            }

            $reportData[] = ['plan' => $plan, 'months' => $monthData];
        }

        return [
            'sede' => $sede,
            'year' => $year,
            'reportData' => $reportData,
            'months' => $months,
            'branding' => BrandingService::forUser(auth()->user()),
        ];
    }
}
