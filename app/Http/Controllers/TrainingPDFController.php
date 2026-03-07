<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Services\BrandingService;
use Barryvdh\DomPDF\Facade\Pdf;

class TrainingPDFController extends Controller
{
    public function download(int $id)
    {
        $training = Training::with([
            'empresa',
            'sede',
            'responsibleUser',
            'attendances'
        ])->findOrFail($id);

        $branding = BrandingService::forUser($training->responsibleUser);

        $pdf = Pdf::loadView('pdf.training-attendance', [
            'training'    => $training,
            'attendances' => $training->attendances()->orderBy('created_at')->get(),
            'branding'    => $branding,
        ]);

        $filename = 'asistencias_' . str_replace(' ', '_', $training->title) . '_' . $training->id . '.pdf';
        return $pdf->download($filename);
    }

    public function preview(int $id)
    {
        $training = Training::with([
            'empresa',
            'sede',
            'responsibleUser',
            'attendances'
        ])->findOrFail($id);

        $branding = BrandingService::forUser($training->responsibleUser);

        $pdf = Pdf::loadView('pdf.training-attendance', [
            'training'    => $training,
            'attendances' => $training->attendances()->orderBy('created_at')->get(),
            'branding'    => $branding,
        ]);

        $filename = 'asistencias_' . str_replace(' ', '_', $training->title) . '_' . $training->id . '.pdf';
        return $pdf->stream($filename);
    }
}
