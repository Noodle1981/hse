<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMasterReportRequest;
use App\Http\Requests\UpdateMasterReportRequest;
use App\Models\MasterReport;
use App\Services\BrandingService;
use App\Services\MasterReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MasterReportController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly MasterReportService $masterReportService) {}

    public function index(Request $request)
    {
        return Inertia::render(
            'Gestor/MasterReports/Index',
            $this->masterReportService->getIndexData(Auth::user(), $request)
        );
    }

    public function create(Request $request)
    {
        return Inertia::render(
            'Gestor/MasterReports/Create',
            $this->masterReportService->getCreateData(Auth::user(), $request)
        );
    }

    public function store(StoreMasterReportRequest $request)
    {
        $report = $this->masterReportService->createReport(
            Auth::user(),
            $request->validatedWithSafeTime()
        );

        return redirect()->route('master-reports.edit', $report->id)
            ->with('message', 'Informe iniciado correctamente.');
    }

    public function show(MasterReport $masterReport)
    {
        $this->authorize('view', $masterReport);

        return Inertia::render('Gestor/MasterReports/Wizard', array_merge(
            $this->masterReportService->getWizardData($masterReport),
            ['readOnly' => true]
        ));
    }

    public function edit(MasterReport $masterReport)
    {
        $this->authorize('view', $masterReport);

        return Inertia::render(
            'Gestor/MasterReports/Wizard',
            $this->masterReportService->getWizardData($masterReport)
        );
    }

    public function update(UpdateMasterReportRequest $request, MasterReport $masterReport)
    {
        $this->authorize('update', $masterReport);

        $masterReport->update($request->safeData());

        $redirectMessage = $this->masterReportService->handleUpdateTransition(
            Auth::user(),
            $masterReport,
            $request
        );

        if ($redirectMessage) {
            return redirect()->route('master-reports.index')->with('message', $redirectMessage);
        }

        return back()->with('message', 'Borrador guardado.');
    }

    public function preview(MasterReport $masterReport)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        $this->authorize('view', $masterReport);

        $wizardData = $this->masterReportService->getWizardData($masterReport);
        $branding = BrandingService::forUser($masterReport->tecnico ?? $masterReport->gestor);

        $pdf = Pdf::loadView('pdf.master-report-pdf', [
            'report' => $wizardData['report'],
            'data' => $wizardData['periodData'],
            'branding' => $branding,
        ]);

        return $pdf->stream("Informe_Maestro_{$masterReport->periodo}.pdf");
    }
}
