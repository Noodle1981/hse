<?php

namespace App\Http\Controllers;

use App\Models\VisitCertificate;
use Barryvdh\DomPDF\Facade\Pdf;

class VisitCertificatePDFController extends Controller
{
    public function preview($id)
    {
        $visit = VisitCertificate::with([
            'empresa',
            'sede',
            'user',
            'checklistCompletions.assignment.checklist',
            'trainings.training',
            'eppDeliveries.employee',
            'hallazgos.fotos',
            'incidentes.fotos'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.visit-certificate-report', compact('visit'));
        
        return $pdf->stream('constancia-visita-' . $visit->id . '.pdf');
    }

    public function download($id)
    {
        $visit = VisitCertificate::with([
            'empresa',
            'sede',
            'user',
            'checklistCompletions.assignment.checklist',
            'trainings.training',
            'eppDeliveries.employee',
            'hallazgos.fotos',
            'incidentes.fotos'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.visit-certificate-report', compact('visit'));
        
        return $pdf->download('constancia-visita-' . $visit->id . '.pdf');
    }
}
