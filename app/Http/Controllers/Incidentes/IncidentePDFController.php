<?php

namespace App\Http\Controllers\Incidentes;

use App\Http\Controllers\Controller;
use App\Services\BrandingService;
use App\Services\PdfReportService;
use Barryvdh\DomPDF\Facade\Pdf;

class IncidentePDFController extends Controller
{
    public function __construct(protected PdfReportService $pdfReportService) {}

    public function download(int $id)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $incidente = $this->pdfReportService->loadIncidenteForPdf($id);
        $branding = BrandingService::forUser($incidente->user);

        $pdf = Pdf::loadView('pdf.incidente-report', [
            'incident' => $incidente,
            'branding' => $branding,
        ]);

        $filename = 'reporte_incidente_'.$incidente->id.'_'.date('Ymd_His').'.pdf';

        return $pdf->download($filename);
    }

    public function preview(int $id)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $incidente = $this->pdfReportService->loadIncidenteForPdf($id);
        $branding = BrandingService::forUser($incidente->user);

        $pdf = Pdf::loadView('pdf.incidente-report', [
            'incident' => $incidente,
            'branding' => $branding,
        ]);

        return $pdf->stream('reporte_incidente_'.$incidente->id.'.pdf');
    }
}
