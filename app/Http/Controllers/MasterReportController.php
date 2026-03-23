<?php

namespace App\Http\Controllers;

use App\Models\MasterReport;
use App\Models\Sede;
use App\Services\BrandingService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MasterReportController extends Controller
{
    public function index()
    {
        return view('master-reports.index');
    }

    public function create()
    {
        return view('master-reports.create');
    }

    public function edit($id)
    {
        return view('master-reports.edit', compact('id'));
    }

    public function preview($id)
    {
        // Aumentar límites para procesar múltiples imágenes en DomPDF sin causar Error 500
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        $report   = MasterReport::with(['sede.empresa', 'tecnico', 'gestor'])->findOrFail($id);
        $data     = $this->aggregatePeriodData($report);
        $branding = BrandingService::forUser($report->tecnico ?? $report->gestor);

        $pdf = Pdf::loadView('pdf.master-report-pdf', [
            'report'   => $report,
            'data'     => $data,
            'branding' => $branding,
        ]);

        return $pdf->stream("Informe_Maestro_{$report->periodo}.pdf");
    }

    private function aggregatePeriodData(MasterReport $report)
    {
        $startDate = Carbon::parse($report->periodo . '-01')->startOfMonth();
        $endDate = Carbon::parse($report->periodo . '-01')->endOfMonth();

        $visits = $report->sede->visitCertificates()
            ->with([
                'checklistCompletions.assignment.checklist', 
                'trainings', 
                'hallazgos.fotos', 
                'incidentes.fotos'
            ])
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->whereIn('status', ['completed', 'signed'])
            ->get();

        return [
            'visits' => $visits,
            'checklists' => $visits->flatMap->checklistCompletions,
            'trainings' => $visits->flatMap->trainings,
            'hallazgos' => $visits->flatMap->hallazgos,
            'incidentes' => $visits->flatMap->incidentes,
            'annual_plan' => $report->sede->annualPlans()->where('year', $startDate->year)->get(),
        ];
    }


}
