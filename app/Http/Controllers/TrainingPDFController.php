<?php

namespace App\Http\Controllers;

use App\Models\Training;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TrainingPDFController extends Controller
{
    /**
     * Genera y descarga el PDF de asistencias de una capacitación
     */
    public function download(int $id)
    {
        $training = Training::with([
            'empresa',
            'sede',
            'responsibleUser',
            'attendances'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.training-attendance', [
            'training' => $training,
            'attendances' => $training->attendances()->orderBy('created_at')->get(),
        ]);

        $filename = 'asistencias_' . str_replace(' ', '_', $training->title) . '_' . $training->id . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Muestra el PDF en el navegador (preview)
     */
    public function preview(int $id)
    {
        $training = Training::with([
            'empresa',
            'sede',
            'responsibleUser',
            'attendances'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.training-attendance', [
            'training' => $training,
            'attendances' => $training->attendances()->orderBy('created_at')->get(),
        ]);

        $filename = 'asistencias_' . str_replace(' ', '_', $training->title) . '_' . $training->id . '.pdf';
        return $pdf->stream($filename);
    }
}
