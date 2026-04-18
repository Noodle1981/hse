<?php

namespace App\Http\Controllers;

use App\Models\DynamicInspection;
use App\Services\BrandingService;
use Barryvdh\DomPDF\Facade\Pdf;

class InspectionPDFController extends Controller
{
    public function download(int $id)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $inspection = DynamicInspection::with([
            'checklist.sections',
            'user',
            'sede.empresa',
            'answers.question'
        ])->findOrFail($id);

        \Illuminate\Support\Facades\Gate::authorize('view', $inspection);

        $branding = BrandingService::forUser($inspection->user);

        $pdf = Pdf::loadView('pdf.inspection', [
            'inspection' => $inspection,
            'answers'    => $inspection->answers,
            'sections'   => $inspection->checklist->sections->sortBy('order'),
            'branding'   => $branding,
        ]);

        return $pdf->download('reporte_inspeccion_' . $inspection->id . '.pdf');
    }

    public function preview(int $id)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $inspection = DynamicInspection::with([
            'checklist.sections',
            'user',
            'sede.empresa',
            'answers.question'
        ])->findOrFail($id);

        \Illuminate\Support\Facades\Gate::authorize('view', $inspection);

        $branding = BrandingService::forUser($inspection->user);

        $pdf = Pdf::loadView('pdf.inspection', [
            'inspection' => $inspection,
            'answers'    => $inspection->answers,
            'sections'   => $inspection->checklist->sections->sortBy('order'),
            'branding'   => $branding,
        ]);

        return $pdf->stream('reporte_inspeccion_' . $inspection->id . '.pdf');
    }
}
