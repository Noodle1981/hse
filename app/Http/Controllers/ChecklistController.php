<?php

namespace App\Http\Controllers;

use App\Models\DynamicChecklist;
use App\Services\ChecklistBuilderService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChecklistController extends Controller
{
    public function __construct(protected ChecklistBuilderService $checklistService) {}

    public function index(Request $request)
    {
        return Inertia::render('Gestor/Checklists/Index', $this->checklistService->getIndexData($request));
    }

    public function toggleStatus(DynamicChecklist $checklist)
    {
        $checklist->update(['is_active' => ! $checklist->is_active]);
        $status = $checklist->is_active ? 'activado' : 'desactivado';

        return back()->with('message', "Checklist $status correctamente.");
    }

    public function duplicate(DynamicChecklist $original)
    {
        $copy = $this->checklistService->duplicate($original);

        return redirect()->route('gestor.checklists.edit', $copy->id)
            ->with('message', '¡Checklist duplicado! Edita el título y guarda para activarlo.');
    }

    public function create()
    {
        return Inertia::render('Gestor/Checklists/Builder', [
            'checklist' => null,
            'categories' => $this->checklistService->getActiveCategories(),
        ]);
    }

    public function edit($id)
    {
        return Inertia::render('Gestor/Checklists/Builder', $this->checklistService->getEditData($id));
    }

    public function save(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'questions' => 'required|array|min:1',
        ]);

        $id = $request->input('id');
        $this->checklistService->saveChecklist($request);

        return redirect()->route('gestor.checklists.index')
            ->with('message', $id ? 'Checklist actualizado.' : 'Checklist creado correctamente.');
    }

    public function emulate(DynamicChecklist $checklist)
    {
        $checklist->load(['sections.questions']);

        return Inertia::render('Tecnico/Inspections/Wizard', [
            'checklist' => $checklist,
            'isEmulation' => true,
        ]);
    }
}
