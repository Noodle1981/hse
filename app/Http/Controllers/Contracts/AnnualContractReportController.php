<?php

namespace App\Http\Controllers\Contracts;

use App\Http\Controllers\Controller;
use App\Services\AnnualContractReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AnnualContractReportController extends Controller
{
    public function __construct(protected AnnualContractReportService $reportService) {}

    public function export(Request $request)
    {
        $sedeId = $request->query('sede_id');
        $year = $request->query('year', date('Y'));

        $data = $this->reportService->buildAnnualReportData($sedeId, $year);
        $sede = $data['sede'];

        $pdf = Pdf::loadView('pdf.annual-plan', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream("plan_anual_{$sede->nombre_sede}_{$year}.pdf");
    }
}
