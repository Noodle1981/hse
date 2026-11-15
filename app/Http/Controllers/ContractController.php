<?php

namespace App\Http\Controllers;

use App\Services\AnnualContractService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ContractController extends Controller
{
    public function __construct(private readonly AnnualContractService $contractService) {}

    public function index(Request $request)
    {
        return Inertia::render(
            'Gestor/Contracts/Index',
            $this->contractService->getIndexData(Auth::user(), $request)
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sede_id' => 'required|exists:sedes,id',
            'activity_name' => 'required|string|max:255',
            'activity_code' => 'nullable|string|max:255',
            'checklist_id' => 'nullable|integer',
            'category' => 'required|in:Capacitación,Inspección,Informe',
            'frequency' => 'required|in:none,mensual,bimestral,trimestral',
            'contract_start' => 'required|string',
            'duration' => 'required|integer',
            'duration_minutes' => 'nullable|integer',
            'modality' => 'nullable|string|in:presencial,virtual,hibrido',
            'multi_trainer' => 'nullable|boolean',
        ]);

        $this->contractService->createPlan($validated);

        return back()->with('message', 'Actividad registrada.');
    }

    public function toggleMonth(Request $request, $id)
    {
        $this->contractService->toggleMonth($id, (string) $request->month);

        return back();
    }

    public function updateDate(Request $request, $id)
    {
        $this->contractService->updateDate($id, (string) $request->month, $request->date);

        return back();
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'activity_name' => 'required|string|max:255',
            'category' => 'required|in:Capacitación,Inspección,Informe',
            'activity_code' => 'nullable|string|max:255',
            'checklist_id' => 'nullable|integer',
            'frequency' => 'required|in:none,mensual,bimestral,trimestral',
            'duration_minutes' => 'nullable|integer',
            'modality' => 'nullable|string|in:presencial,virtual,hibrido',
            'multi_trainer' => 'nullable|boolean',
        ]);

        $this->contractService->updatePlan($id, $validated);

        return back()->with('message', 'Actividad actualizada.');
    }

    public function destroy($id)
    {
        $this->contractService->deletePlan($id);

        return back()->with('message', 'Actividad eliminada.');
    }
}
