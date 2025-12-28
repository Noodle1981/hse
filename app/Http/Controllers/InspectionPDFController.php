<?php

namespace App\Http\Controllers;

use App\Models\DynamicInspection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InspectionPDFController extends Controller
{
    /**
     * Genera y descarga el PDF de una inspección completada
     */
    public function download(int $id)
    {
        $inspection = DynamicInspection::with([
            'checklist.sections',
            'user',
            'sede.empresa',
            'answers.question'
        ])->findOrFail($id);

        // Seguridad
        if (auth()->user()->rol === 'Técnico' && $inspection->user_id !== auth()->id()) {
            abort(403);
        }

        $pdf = Pdf::loadView('pdf.inspection', [
            'inspection' => $inspection,
            'answers' => $inspection->answers,
            'sections' => $inspection->checklist->sections->sortBy('order'),
        ]);

        $filename = 'reporte_inspeccion_' . $inspection->id . '.pdf';
        return $pdf->download($filename);
    }

    public function preview(int $id)
    {
        $inspection = DynamicInspection::with([
            'checklist.sections',
            'user',
            'sede.empresa',
            'answers.question'
        ])->findOrFail($id);

        // Seguridad
        if (auth()->user()->rol === 'Técnico' && $inspection->user_id !== auth()->id()) {
            abort(403);
        }

        $pdf = Pdf::loadView('pdf.inspection', [
            'inspection' => $inspection,
            'answers' => $inspection->answers,
            'sections' => $inspection->checklist->sections->sortBy('order'),
        ]);

        return $pdf->stream('reporte_inspeccion_' . $inspection->id . '.pdf');
    }
}
