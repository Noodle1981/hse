<?php

namespace App\Http\Controllers\Risks;

use App\Http\Controllers\Controller;
use App\Models\RiskMatrix;
use App\Models\RiskEvaluation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiskMatrixReportController extends Controller
{
    public function dashboardExport(Request $request)
    {
        $matrixId = $request->query('matrix_id');
        
        // Cargar datos del heatmap (Lógica movida desde RiskDashboard para consistencia)
        $heatmapData = [];
        for ($s = 5; $s >= 1; $s--) {
            for ($p = 1; $p <= 5; $p++) {
                $heatmapData[$s][$p] = 0;
            }
        }

        $subquery = DB::table('risk_evaluations')
            ->select('risk_id', DB::raw('MAX(id) as last_id'))
            ->groupBy('risk_id');

        $query = RiskEvaluation::query()
            ->joinSub($subquery, 'latest_evals', function ($join) {
                $join->on('risk_evaluations.id', '=', 'latest_evals.last_id');
            });

        $user = auth()->user();
        if ($user->rol === 'Técnico') {
            $query->whereHas('risk.matrix', function($q) use ($user) {
                $q->whereIn('sede_id', $user->sedes()->pluck('sedes.id'));
            });
        }

        if ($matrixId) {
            $query->whereHas('risk', function($q) use ($matrixId) {
                $q->where('risk_matrix_id', $matrixId);
            });
        }

        $latestEvaluations = $query->get();
        $kpis = ['total_risks' => 0, 'critical' => 0, 'high' => 0, 'medium' => 0, 'low' => 0];
        
        foreach ($latestEvaluations as $eval) {
            $heatmapData[$eval->severity][$eval->probability]++;
            $kpis['total_risks']++;
            if ($eval->level === 'Crítico') $kpis['critical']++;
            elseif ($eval->level === 'Alto') $kpis['high']++;
            elseif ($eval->level === 'Medio') $kpis['medium']++;
            else $kpis['low']++;
        }

        $matrix = $matrixId ? \App\Models\RiskMatrix::with(['empresa', 'sede'])->find($matrixId) : null;

        $pdf = Pdf::loadView('reports.risk-dashboard', [
            'heatmapData' => $heatmapData,
            'kpis' => $kpis,
            'matrix' => $matrix,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream("dashboard_riesgos_" . now()->format('Ymd_His') . ".pdf");
    }

    public function export($id)
    {
        $matrix = RiskMatrix::with([
            'risks.controls', 
            'risks.latestEvaluation', 
            'empresa', 
            'sede'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('reports.risk-matrix', compact('matrix'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream("matriz_riesgo_{$id}.pdf");
    }
}
