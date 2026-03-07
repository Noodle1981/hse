<?php

namespace App\Http\Controllers;

use App\Models\VisitCertificate;
use App\Services\BrandingService;
use Barryvdh\DomPDF\Facade\Pdf;

class VisitCertificatePDFController extends Controller
{
    private function loadVisit($id): VisitCertificate
    {
        return VisitCertificate::with([
            'empresa',
            'sede',
            'user',
            'checklistCompletions.assignment.checklist',
            'trainings.training',
            'hallazgos.fotos',
            'incidentes.fotos'
        ])->findOrFail($id);
    }

    public function preview($id)
    {
        $visit    = $this->loadVisit($id);
        $branding = BrandingService::forUser($visit->user);

        $pdf = Pdf::loadView('pdf.visit-certificate-report', compact('visit', 'branding'));
        return $pdf->stream('constancia-visita-' . $visit->id . '.pdf');
    }

    public function download($id)
    {
        $visit    = $this->loadVisit($id);
        $branding = BrandingService::forUser($visit->user);

        $pdf = Pdf::loadView('pdf.visit-certificate-report', compact('visit', 'branding'));
        return $pdf->download('constancia-visita-' . $visit->id . '.pdf');
    }

    public function checklistPreview($id)
    {
        $completion = \App\Models\VisitChecklistCompletion::with([
            'visit.empresa', 'visit.sede', 'visit.user',
            'assignment.checklist.sections'
        ])->findOrFail($id);

        $branding = BrandingService::forUser($completion->visit->user);

        $pdf = Pdf::loadView('pdf.checklist-standalone', compact('completion', 'branding'));
        return $pdf->stream('inspeccion-' . $completion->id . '.pdf');
    }

    public function checklistDownload($id)
    {
        $completion = \App\Models\VisitChecklistCompletion::with([
            'visit.empresa', 'visit.sede', 'visit.user',
            'assignment.checklist.sections'
        ])->findOrFail($id);

        $branding = BrandingService::forUser($completion->visit->user);

        $pdf = Pdf::loadView('pdf.checklist-standalone', compact('completion', 'branding'));
        return $pdf->download('inspeccion-' . $completion->id . '.pdf');
    }
}
