<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChecklistCompletionRequest;
use App\Models\DynamicInspection;
use App\Services\InspectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InspectionController extends Controller
{
    public function __construct(private readonly InspectionService $inspectionService) {}

    /**
     * Start/Resume an inspection wizard.
     */
    public function wizard(Request $request, $checklist_id, $assignmentId = null)
    {
        $result = $this->inspectionService->prepareWizardData(Auth::user(), $request, $checklist_id, $assignmentId);

        if ($result['already_completed']) {
            return redirect($result['redirect'])->with('error', 'Esta asignación ya ha sido completada.');
        }

        return Inertia::render('Tecnico/Inspections/Wizard', $result['props']);
    }

    /**
     * Save partial progress.
     */
    public function saveProgress(Request $request, $id, $inspection)
    {
        $this->inspectionService->saveProgressById($request, $inspection);

        return back()->with('message', 'Progreso sincronizado.');
    }

    /**
     * Complete inspection and dispatch SRE / Visit completion.
     */
    public function complete(StoreChecklistCompletionRequest $request, $id, $inspection): RedirectResponse
    {
        $redirect = $this->inspectionService->completeInspection($request, Auth::user(), $inspection);

        return redirect($redirect)->with('message', 'Inspección finalizada correctamente.');
    }

    /**
     * Historial de inspecciones para el Gestor.
     */
    public function historyGestor(Request $request)
    {
        return Inertia::render(
            'Gestor/Inspections/Index',
            $this->inspectionService->getGestorHistory(Auth::user(), $request)
        );
    }

    /**
     * Historial de inspecciones para el Técnico.
     */
    public function historyTecnico(Request $request)
    {
        return Inertia::render(
            'Tecnico/Inspections/History',
            $this->inspectionService->getTecnicoHistory(Auth::user(), $request)
        );
    }

    /**
     * Ver detalle de una inspección.
     */
    public function show(DynamicInspection $inspection)
    {
        $inspection->load(['checklist.sections.questions', 'checklist.questions', 'user', 'sede.empresa', 'answers.question']);

        $component = Auth::user()->isTechnician()
            ? 'Tecnico/Inspections/Show'
            : 'Gestor/Inspections/Show';

        return Inertia::render($component, [
            'inspection' => $inspection,
        ]);
    }

    /**
     * Re-vincula la inspección a un activo diferente (o la desvincula).
     */
    public function relinkAsset(Request $request, DynamicInspection $inspection)
    {
        $user = Auth::user();
        if ($user->isTechnician()) {
            abort(403, 'Los técnicos no tienen permiso para re-vincular activos.');
        }

        $validated = $request->validate([
            'asset_id' => 'nullable|exists:assets,id',
        ]);

        $this->inspectionService->relinkAsset($user, $inspection, $validated['asset_id'] ?? null);

        return back()->with('message', 'Activo re-vinculado con éxito y alertas actualizadas.');
    }
}
