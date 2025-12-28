<?php

namespace App\Http\Controllers\Risks;

use App\Http\Controllers\Controller;
use App\Models\RiskMatrix;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RiskMatrixReportController extends Controller
{
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
