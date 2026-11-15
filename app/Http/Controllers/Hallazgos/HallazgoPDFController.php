<?php

namespace App\Http\Controllers\Hallazgos;

use App\Http\Controllers\Controller;
use App\Services\BrandingService;
use App\Services\PdfReportService;
use Barryvdh\DomPDF\Facade\Pdf;

class HallazgoPDFController extends Controller
{
    public function __construct(protected PdfReportService $pdfReportService) {}

    public function download(int $id)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $hallazgo = $this->pdfReportService->loadHallazgoForPdf($id);
        $branding = BrandingService::forUser($hallazgo->user);

        $pdf = Pdf::loadView('pdf.hallazgo-report', [
            'finding' => $hallazgo,
            'seguimientos' => $hallazgo->seguimientos->sortByDesc('fecha_seguimiento'),
            'fotos' => $hallazgo->fotos,
            'branding' => $branding,
        ]);

        $filename = 'reporte_hallazgo_'.$hallazgo->id.'_'.date('Ymd_His').'.pdf';

        return $pdf->download($filename);
    }

    public function preview(int $id)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $hallazgo = $this->pdfReportService->loadHallazgoForPdf($id);
        $branding = BrandingService::forUser($hallazgo->user);

        $pdf = Pdf::loadView('pdf.hallazgo-report', [
            'finding' => $hallazgo,
            'seguimientos' => $hallazgo->seguimientos->sortByDesc('fecha_seguimiento'),
            'fotos' => $hallazgo->fotos,
            'branding' => $branding,
        ]);

        return $pdf->stream('reporte_hallazgo_'.$hallazgo->id.'.pdf');
    }
}
