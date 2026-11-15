<?php

namespace App\Http\Controllers;

use App\Services\PdfReportService;
use Barryvdh\DomPDF\Facade\Pdf;

class TrainingPDFController extends Controller
{
    public function __construct(protected PdfReportService $pdfReportService) {}

    public function download(int $id)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $data = $this->pdfReportService->resolveTrainingForPdf($id);
        $training = $data['training'];

        $pdf = Pdf::loadView('pdf.training-attendance', $data);
        $filename = 'asistencias_'.str_replace(' ', '_', $training->title).'_'.$training->id.'.pdf';

        return $pdf->download($filename);
    }

    public function preview(int $id)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $data = $this->pdfReportService->resolveTrainingForPdf($id);
        $training = $data['training'];

        $pdf = Pdf::loadView('pdf.training-attendance', $data);
        $filename = 'asistencias_'.str_replace(' ', '_', $training->title).'_'.$training->id.'.pdf';

        return $pdf->stream($filename);
    }
}
