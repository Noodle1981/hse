<?php

namespace App\Http\Controllers\Contracts;

use App\Http\Controllers\Controller;
use App\Models\AnnualPlan;
use App\Models\DynamicInspection;
use App\Models\MasterReport;
use App\Models\Sede;
use App\Models\Training;
use App\Models\VisitTraining;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnnualContractReportController extends Controller
{
    public function export(Request $request)
    {
        $sedeId = $request->query('sede_id');
        $year = $request->query('year', date('Y'));

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

        // --- OPTIMIZATION: PRE-LOAD ALL COMPLIANCE FOR THE YEAR ---
        $startDate = Carbon::create($year, 1, 1)->startOfDay();
        $endDate = Carbon::create($year, 12, 31)->endOfDay();

        // 1. Inspections
        $insLookup = DynamicInspection::where('sede_id', $sedeId)
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get()
            ->groupBy(fn ($i) => $i->dynamic_checklist_id.'_'.$i->completed_at->format('Y-m'));

        // 2. Trainings (Direct & via VisitTraining)
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

        // 3. Reports
        $repLookup = MasterReport::where('sede_id', $sedeId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(fn ($r) => str_contains($r->periodo, '-') ? $r->periodo : $r->created_at->format('Y-m'));

        $reportData = [];
        foreach ($plans as $plan) {
            $monthData = [];
            foreach ($months as $name => $num) {
                $monthStr = $year.'-'.str_pad($num, 2, '0', STR_PAD_LEFT);

                // 1. Is it scheduled?
                $isScheduled = in_array($monthStr, $plan->scheduled_months ?? []);
                if (! $isScheduled) {
                    $monthData[$name] = ['status' => 'none', 'count' => 0];

                    continue;
                }

                // 2. Check Compliance
                $count = 0;
                if ($plan->category === 'Inspección' && $plan->checklist_id) {
                    $count = isset($insLookup[$plan->checklist_id.'_'.$monthStr]) ? $insLookup[$plan->checklist_id.'_'.$monthStr]->count() : 0;
                } elseif ($plan->category === 'Capacitación') {
                    if ($plan->checklist_id) {
                        $count = isset($trainChecklistLookup[$plan->checklist_id][$monthStr]) ? 1 : 0;
                    } else {
                        $count = isset($trainLookup[mb_strtolower($plan->activity_name)][$monthStr]) ? 1 : 0;
                    }
                } elseif ($plan->category === 'Informe') {
                    $count = isset($repLookup[$monthStr]) ? $repLookup[$monthStr]->count() : 0;
                }

                if ($count > 0) {
                    $monthData[$name] = ['status' => 'completed', 'count' => $count];
                } else {
                    if ($plan->status === 'suspended') {
                        $monthData[$name] = ['status' => 'suspended', 'count' => 0];
                    } elseif ($plan->status === 'cancelled') {
                        $monthData[$name] = ['status' => 'cancelled', 'count' => 0];
                    } else {
                        $currentDate = now()->format('Y-m');
                        $status = $monthStr < $currentDate ? 'overdue' : 'pending';
                        $monthData[$name] = ['status' => $status, 'count' => 0];
                    }
                }
            }

            $reportData[] = [
                'plan' => $plan,
                'months' => $monthData,
            ];
        }

        $pdf = Pdf::loadView('pdf.annual-plan', compact('sede', 'year', 'reportData', 'months'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream("plan_anual_{$sede->nombre_sede}_{$year}.pdf");
    }
}
