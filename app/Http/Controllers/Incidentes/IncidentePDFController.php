<?php

namespace App\Http\Controllers\Incidentes;

use App\Http\Controllers\Controller;
use App\Models\Incidente;
use App\Services\BrandingService;
use Barryvdh\DomPDF\Facade\Pdf;

class IncidentePDFController extends Controller
{
    public function download(int $id)
    {
        $incidente = Incidente::with([
            'sede.empresa', 'user', 'fotos', 'hallazgo', 'visit'
        ])->findOrFail($id);

        $branding = BrandingService::forUser($incidente->user);

        $pdf = Pdf::loadView('pdf.incidente-report', [
            'incident' => $incidente,
            'branding' => $branding,
        ]);

        $filename = 'reporte_incidente_' . $incidente->id . '_' . date('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    public function preview(int $id)
    {
        $incidente = Incidente::with([
            'sede.empresa', 'user', 'fotos', 'hallazgo', 'visit'
        ])->findOrFail($id);

        $branding = BrandingService::forUser($incidente->user);

        $pdf = Pdf::loadView('pdf.incidente-report', [
            'incident' => $incidente,
            'branding' => $branding,
        ]);

        return $pdf->stream('reporte_incidente_' . $incidente->id . '.pdf');
    }
}
