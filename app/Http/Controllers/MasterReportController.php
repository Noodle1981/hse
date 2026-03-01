<?php

namespace App\Http\Controllers;

use App\Models\MasterReport;
use App\Models\Sede;
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
        $report = MasterReport::with(['sede.empresa', 'tecnico', 'gestor'])->findOrFail($id);
        
        // Simular agregación de datos si no están en datos_anexo
        $data = $this->aggregatePeriodData($report);
        
        $pdf = Pdf::loadView('pdf.master-report-pdf', [
            'report' => $report,
            'data' => $data
        ]);

        return $pdf->stream("Informe_Maestro_{$report->periodo}.pdf");
    }

    private function aggregatePeriodData(MasterReport $report)
    {
        $startDate = Carbon::parse($report->periodo . '-01')->startOfMonth();
        $endDate = Carbon::parse($report->periodo . '-01')->endOfMonth();

        $visits = $report->sede->visitCertificates()
            ->with(['checklistCompletions.assignment.checklist', 'trainings', 'eppDeliveries', 'hallazgos', 'incidentes'])
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->whereIn('status', ['completed', 'signed'])
            ->get();

        return [
            'hallazgos' => $visits->flatMap->hallazgos,
            'incidentes' => $visits->flatMap->incidentes,
            'annual_plan' => $report->sede->annualPlans()->where('year', $startDate->year)->get(),
        ];
    }


}
