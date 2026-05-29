<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Services\BrandingService;
use Barryvdh\DomPDF\Facade\Pdf;

class TrainingPDFController extends Controller
{
    public function download(int $id)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        // 1. Intentar buscar ejecución en la visita (VisitTraining)
        $visitTraining = \App\Models\VisitTraining::with([
            'visit.sede.empresa',
            'visit.user',
            'training.responsibleUser'
        ])->find($id);

        if ($visitTraining) {
            $training = $visitTraining;
            // Enriquecer dinámicamente para compatibilidad con la vista del PDF
            $training->status = 'completed';
            $training->sede = $visitTraining->visit->sede ?? null;
            $training->empresa = $visitTraining->visit->sede->empresa ?? null;
            $training->responsibleUser = $visitTraining->visit->user ?? ($visitTraining->training->responsibleUser ?? null);
            $training->trainer_name = $visitTraining->visit->user ? $visitTraining->visit->user->nombres : ($visitTraining->training->trainer_name ?? 'Instructor Externo');
            $training->date = $visitTraining->created_at;
            $training->duration = $visitTraining->duration_minutes;
            
            // Cargar asistentes desde la relación
            $training->attendances = $visitTraining->attendances()->orderBy('created_at')->get();
        } else {
            // Fallback heredado a plantilla base o inyección directa
            $training = Training::with([
                'empresa',
                'sede',
                'responsibleUser',
                'attendances'
            ])->findOrFail($id);
        }

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
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        // 1. Intentar buscar ejecución en la visita (VisitTraining)
        $visitTraining = \App\Models\VisitTraining::with([
            'visit.sede.empresa',
            'visit.user',
            'training.responsibleUser'
        ])->find($id);

        if ($visitTraining) {
            $training = $visitTraining;
            // Enriquecer dinámicamente para compatibilidad con la vista del PDF
            $training->status = 'completed';
            $training->sede = $visitTraining->visit->sede ?? null;
            $training->empresa = $visitTraining->visit->sede->empresa ?? null;
            $training->responsibleUser = $visitTraining->visit->user ?? ($visitTraining->training->responsibleUser ?? null);
            $training->trainer_name = $visitTraining->visit->user ? $visitTraining->visit->user->nombres : ($visitTraining->training->trainer_name ?? 'Instructor Externo');
            $training->date = $visitTraining->created_at;
            $training->duration = $visitTraining->duration_minutes;
            
            // Cargar asistentes desde la relación
            $training->attendances = $visitTraining->attendances()->orderBy('created_at')->get();
        } else {
            // Fallback heredado a plantilla base o inyección directa
            $training = Training::with([
                'empresa',
                'sede',
                'responsibleUser',
                'attendances'
            ])->findOrFail($id);
        }

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
