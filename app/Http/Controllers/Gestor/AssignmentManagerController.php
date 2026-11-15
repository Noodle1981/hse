<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\ChecklistAssignment;
use App\Services\GestorToolsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AssignmentManagerController extends Controller
{
    public function __construct(private readonly GestorToolsService $toolsService) {}

    public function index(Request $request)
    {
        return Inertia::render(
            'Gestor/Assignments/Index',
            $this->toolsService->getAssignmentsIndexData(Auth::user(), $request)
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dynamic_checklist_id' => 'required|exists:dynamic_checklists,id',
            'sede_id' => 'required|exists:sedes,id',
            'user_id' => 'nullable|exists:users,id',
            'due_date' => 'required|date',
            'frequency' => 'nullable|string|in:once,weekly,monthly,quarterly',
            'notes' => 'nullable|string|max:500',
        ]);

        $this->toolsService->createAssignment(Auth::user(), $validated);

        return back()->with('message', 'Asignación despachada correctamente.');
    }

    public function destroy(ChecklistAssignment $assignment)
    {
        $user = Auth::user();
        $assignment->loadMissing('sede.empresa');

        if (! $user->isSuperAdmin() && $assignment->sede?->empresa?->consultora_id !== $user->consultora_id) {
            abort(403, 'Acceso no autorizado.');
        }

        $assignment->delete();

        return back()->with('message', 'Asignación eliminada.');
    }
}
