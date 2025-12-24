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
            'checklist',
            'user',
            'answers.question'
        ])->findOrFail($id);

        // Organizar respuestas por orden
        $answers = $inspection->answers->sortBy(function ($answer) {
            return $answer->question->order ?? 0;
        });

        $pdf = Pdf::loadView('pdf.inspection', [
            'inspection' => $inspection,
            'answers' => $answers,
        ]);

        $filename = 'inspeccion_' . $inspection->id . '_' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Muestra el PDF en el navegador (preview)
     */
    public function preview(int $id)
    {
        $inspection = DynamicInspection::with([
            'checklist',
            'user',
            'answers.question'
        ])->findOrFail($id);

        $answers = $inspection->answers->sortBy(function ($answer) {
            return $answer->question->order ?? 0;
        });

        $pdf = Pdf::loadView('pdf.inspection', [
            'inspection' => $inspection,
            'answers' => $answers,
        ]);

        return $pdf->stream('inspeccion_' . $inspection->id . '.pdf');
    }
}
