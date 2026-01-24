<?php

namespace App\Http\Controllers\Incidentes;

use App\Http\Controllers\Controller;
use App\Models\Incidente;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class IncidentePDFController extends Controller
{
    /**
     * Generar y descargar el reporte del incidente en PDF
     */
    public function download(int $id)
    {
        $incidente = Incidente::with([
            'sede.empresa',
            'user',
            'fotos',
            'hallazgo',
            'visit'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.incidente-report', [
            'incident' => $incidente,
        ]);

        $filename = 'reporte_incidente_' . $incidente->id . '_' . date('Ymd_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Previsualizar el PDF en el navegador
     */
    public function preview(int $id)
    {
        $incidente = Incidente::with([
            'sede.empresa',
            'user',
            'fotos',
            'hallazgo',
            'visit'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.incidente-report', [
            'incident' => $incidente,
        ]);

        return $pdf->stream('reporte_incidente_' . $incidente->id . '.pdf');
    }
}
