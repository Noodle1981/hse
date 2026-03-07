<?php

namespace App\Http\Controllers\Contracts;

use App\Http\Controllers\Controller;
use App\Models\AnnualPlan;
use App\Models\Sede;
use App\Models\DynamicInspection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AnnualContractReportController extends Controller
{
    public function export(Request $request)
    {
        $sedeId = $request->query('sede_id');
        $year = $request->query('year', date('Y'));

        $sede = Sede::with('empresa')->findOrFail($sedeId);
        
        $plans = AnnualPlan::where('sede_id', $sedeId)
            ->where('year', $year)
            ->orderBy('category')
            ->get();

        $months = [
            'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4,
            'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8,
            'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12
        ];

        $reportData = [];
        foreach ($plans as $plan) {
            $monthData = [];
            foreach ($months as $name => $num) {
                $monthData[$name] = $this->getComplianceData($plan, $name, $num, $sedeId, $year);
            }
            $reportData[] = [
                'plan' => $plan,
                'months' => $monthData
            ];
        }

        $pdf = Pdf::loadView('reports.annual-plan', compact('sede', 'year', 'reportData', 'months'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream("plan_anual_{$sede->nombre_sede}_{$year}.pdf");
    }

    private function getComplianceData($plan, $monthName, $monthNumber, $sedeId, $year)
    {
        if (!$plan->$monthName) return ['status' => 'none', 'count' => 0];

        $count = 0;
        if ($plan->category === 'Inspección' && $plan->checklist_id) {
            $count = DynamicInspection::where('dynamic_checklist_id', $plan->checklist_id)
                ->where('sede_id', $sedeId)
                ->where('status', 'completed')
                ->whereMonth('completed_at', $monthNumber)
                ->whereYear('completed_at', $year)
                ->count();
        }
        
        if ($count > 0) return ['status' => 'completed', 'count' => $count];
        
        $currentMonth = (int)date('m');
        $currentYear = (int)date('Y');
        
        if ($year < $currentYear || ($year == $currentYear && $monthNumber < $currentMonth)) {
            return ['status' => 'overdue', 'count' => 0];
        }
        
        return ['status' => 'pending', 'count' => 0];
    }
}
