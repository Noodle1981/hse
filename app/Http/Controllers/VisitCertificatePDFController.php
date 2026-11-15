<?php

namespace App\Http\Controllers;

use App\Services\BrandingService;
use App\Services\PdfReportService;
use Barryvdh\DomPDF\Facade\Pdf;

class VisitCertificatePDFController extends Controller
{
    public function __construct(protected PdfReportService $pdfReportService) {}

    public function loading($id)
    {
        $visit = $this->pdfReportService->loadVisitSummary($id);
        $prefix = request()->is('gestor/*') ? 'gestor' : 'tecnico';

        return view('pdf.loading', [
            'title' => 'Generando Constancia',
            'subtitle' => 'Expediente de Visita',
            'description' => 'Sincronizando registros de '.$visit->sede->nombre_sede.' y compilando firmas digitales...',
            'redirectUrl' => request()->has('download')
                ? route($prefix.'.visits.certificate.download', $id)
                : route($prefix.'.visits.certificate.preview', $id),
        ]);
    }

    public function preview($id)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $visit = $this->pdfReportService->loadVisitFull($id);
        $branding = BrandingService::forUser($visit->user);

        $pdf = Pdf::loadView('pdf.visit-certificate-report', compact('visit', 'branding'));

        return $pdf->stream('constancia-visita-'.$visit->id.'.pdf');
    }

    public function download($id)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $visit = $this->pdfReportService->loadVisitFull($id);
        $branding = BrandingService::forUser($visit->user);

        $pdf = Pdf::loadView('pdf.visit-certificate-report', compact('visit', 'branding'));

        return $pdf->download('constancia-visita-'.$visit->id.'.pdf');
    }

    public function checklistLoading($id)
    {
        $completion = $this->pdfReportService->loadCompletionSummary($id);
        $prefix = request()->is('gestor/*') ? 'gestor' : 'tecnico';

        return view('pdf.loading', [
            'title' => 'Procesando Inspección',
            'subtitle' => 'Checklist Técnico',
            'description' => 'Generando reporte detallado de '.$completion->assignment->checklist->title.'...',
            'redirectUrl' => request()->has('download')
                ? route($prefix.'.visits.checklist.download', $id)
                : route($prefix.'.visits.checklist.preview', $id),
        ]);
    }

    public function checklistPreview($id)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $completion = $this->pdfReportService->loadCompletionFull($id);
        $branding = BrandingService::forUser($completion->visit->user);

        $pdf = Pdf::loadView('pdf.checklist-standalone', compact('completion', 'branding'));

        return $pdf->stream('inspeccion-'.$completion->id.'.pdf');
    }

    public function checklistDownload($id)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $completion = $this->pdfReportService->loadCompletionFull($id);
        $branding = BrandingService::forUser($completion->visit->user);

        $pdf = Pdf::loadView('pdf.checklist-standalone', compact('completion', 'branding'));

        return $pdf->download('inspeccion-'.$completion->id.'.pdf');
    }
}
