<?php

namespace App\Http\Controllers\Hallazgos;

use App\Http\Controllers\Controller;
use App\Models\Hallazgo;
use App\Services\BrandingService;
use Barryvdh\DomPDF\Facade\Pdf;

class HallazgoPDFController extends Controller
{
    public function download(int $id)
    {
        $hallazgo = Hallazgo::with([
            'sede.empresa', 'user', 'seguimientos.user', 'fotos', 'risk.matrix', 'origen'
        ])->findOrFail($id);

        $branding = BrandingService::forUser($hallazgo->user);

        $pdf = Pdf::loadView('pdf.hallazgo-report', [
            'finding'      => $hallazgo,
            'seguimientos' => $hallazgo->seguimientos->sortByDesc('fecha_seguimiento'),
            'fotos'        => $hallazgo->fotos,
            'branding'     => $branding,
        ]);

        $filename = 'reporte_hallazgo_' . $hallazgo->id . '_' . date('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    public function preview(int $id)
    {
        $hallazgo = Hallazgo::with([
            'sede.empresa', 'user', 'seguimientos.user', 'fotos', 'risk.matrix', 'origen'
        ])->findOrFail($id);

        $branding = BrandingService::forUser($hallazgo->user);

        $pdf = Pdf::loadView('pdf.hallazgo-report', [
            'finding'      => $hallazgo,
            'seguimientos' => $hallazgo->seguimientos->sortByDesc('fecha_seguimiento'),
            'fotos'        => $hallazgo->fotos,
            'branding'     => $branding,
        ]);

        return $pdf->stream('reporte_hallazgo_' . $hallazgo->id . '.pdf');
    }
}
