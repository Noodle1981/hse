<?php

namespace App\Http\Controllers\Hallazgos;

use App\Http\Controllers\Controller;
use App\Models\Hallazgo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class HallazgoPDFController extends Controller
{
    /**
     * Generar y descargar el reporte del hallazgo en PDF
     */
    public function download(int $id)
    {
        $hallazgo = Hallazgo::with([
            'sede.empresa',
            'user',
            'seguimientos.user',
            'fotos',
            'risk.matrix',
            'origen'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.hallazgo-report', [
            'hallazgo' => $hallazgo,
            'seguimientos' => $hallazgo->seguimientos->sortByDesc('fecha_seguimiento'),
            'fotos' => $hallazgo->fotos,
        ]);

        $filename = 'reporte_hallazgo_' . $hallazgo->id . '_' . date('Ymd_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Previsualizar el PDF en el navegador
     */
    public function preview(int $id)
    {
        $hallazgo = Hallazgo::with([
            'sede.empresa',
            'user',
            'seguimientos.user',
            'fotos',
            'risk.matrix',
            'origen'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.hallazgo-report', [
            'hallazgo' => $hallazgo,
            'seguimientos' => $hallazgo->seguimientos->sortByDesc('fecha_seguimiento'),
            'fotos' => $hallazgo->fotos,
        ]);

        return $pdf->stream('reporte_hallazgo_' . $hallazgo->id . '.pdf');
    }
}
