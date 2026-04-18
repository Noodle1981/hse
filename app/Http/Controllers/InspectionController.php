<?php

namespace App\Http\Controllers;

use App\Models\DynamicChecklist;
use App\Models\ChecklistAssignment;
use App\Models\DynamicInspection;
use App\Models\DynamicAnswer;
use App\Models\VisitCertificate;
use App\Services\VisitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InspectionController extends Controller
{
    protected $visitService;

    public function __construct(VisitService $visitService)
    {
        $this->visitService = $visitService;
    }

    /**
     * Start/Resume an inspection wizard.
     */
    public function wizard(Request $request, $checklist_id, $assignmentId = null)
    {
        $checklist = DynamicChecklist::with(['sections.questions', 'questions' => function($q) {
            $q->whereNull('section_id');
        }])->findOrFail($checklist_id);

        $assignmentId = $assignmentId ?: $request->query('assignment');
        $visitId = $request->query('visit_id');

        $assignment = null;
        if ($assignmentId) {
            $assignment = ChecklistAssignment::where('id', $assignmentId)
                ->where('user_id', Auth::id())
                ->firstOrFail();
            $assignment->update(['status' => 'in_progress']);
        }

        // Buscar o crear borrador de inspección específico para esta asignación
        $query = DynamicInspection::where('dynamic_checklist_id', $checklist->id)
            ->where('user_id', Auth::id())
            ->where('status', 'draft');

        if ($assignmentId) {
            $query->where('checklist_assignment_id', $assignmentId);
        } else {
            $query->whereNull('checklist_assignment_id');
        }

        $inspection = $query->latest()->first();

        if (!$inspection) {
            $inspection = DynamicInspection::create([
                'dynamic_checklist_id' => $checklist->id,
                'user_id' => Auth::id(),
                'checklist_assignment_id' => $assignmentId,
                'sede_id' => $assignment ? $assignment->sede_id : null,
                'status' => 'draft',
            ]);
        }

        // Cargar respuestas existentes
        $answers = $inspection->answers()->pluck('value', 'dynamic_question_id');

        return Inertia::render('Tecnico/Inspections/Wizard', [
            'checklist' => $checklist,
            'assignment' => $assignment,
            'inspection' => $inspection,
            'visitId' => $visitId,
            'initialAnswers' => $answers,
        ]);
    }

    /**
     * Save partial progress.
     */
    public function saveProgress(Request $request, $id, $inspection)
    {
        $inspection = DynamicInspection::findOrFail($inspection);
        return $this->doSaveProgress($request, $inspection);
    }

    protected function doSaveProgress(Request $request, DynamicInspection $inspection)
    {
        DB::transaction(function () use ($request, $inspection) {
            $answers = $request->input('answers', []);
            
            // Optimización: Eliminar respuestas previas y reinsertar en lote (o updateOrCreate optimizado)
            // Por simplicidad y asegurar unicidad sin depender de constraints únicas de BD, limpiamos y reinsertamos
            $inspection->answers()->delete();
            
            if (!empty($answers)) {
                $batchData = [];
                foreach ($answers as $questionId => $value) {
                    $batchData[] = [
                        'dynamic_inspection_id' => $inspection->id,
                        'dynamic_question_id' => $questionId,
                        'value' => is_array($value) ? json_encode($value) : $value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                DynamicAnswer::insert($batchData);
            }

            $inspection->update(['notes' => $request->input('notes')]);
        });

        return back()->with('message', 'Progreso sincronizado.');
    }

    /**
     * Complete and close the inspection.
     */
    public function complete(Request $request, $id, $inspection)
    {
        $inspection = DynamicInspection::findOrFail($inspection);
        $request->validate([
            'supervisor_name' => 'required|string',
            'supervisor_signature' => 'required|string',
            'technician_signature' => 'required|string',
        ]);

        DB::transaction(function () use ($request, $inspection) {
            // Reutilizar lógica optimizada de guardado de respuestas
            $this->doSaveProgress($request, $inspection);

            $inspection->update([
                'status' => 'completed',
                'completed_at' => now(),
                'notes' => $request->notes,
                'supervisor_name' => $request->supervisor_name,
                'supervisor_signature' => $request->supervisor_signature,
                'technician_signature' => $request->technician_signature,
            ]);

            if ($inspection->checklist_assignment_id) {
                $assignment = ChecklistAssignment::find($inspection->checklist_assignment_id);
                
                if ($assignment) {
                    // Si hay visita vincular
                    if ($request->visit_id) {
                        $visit = VisitCertificate::find($request->visit_id);
                        if ($visit) {
                            $this->visitService->completeChecklist($visit, $assignment, []);
                        } else {
                            // Si se pasó visit_id pero no se encontró la visita (caso raro but robust)
                            $assignment->update(['status' => 'completed']);
                        }
                    } else {
                        $assignment->update(['status' => 'completed']);
                    }
                }
            }
        });

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $redirect = $request->visit_id 
            ? route('tecnico.visits.in-progress', $request->visit_id)
            : ($user->isTechnician() ? route('tecnico.checklists.tasks.index') : route('gestor.inspections.index'));

        return redirect($redirect)->with('message', 'Inspección finalizada correctamente.');
    }

    /**
     * Historial de inspecciones para el Gestor.
     */
    public function historyGestor(Request $request)
    {
        $query = DynamicInspection::with(['checklist', 'user', 'sede.empresa'])
            ->completed()
            ->latest('completed_at');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->whereHas('sede', fn($sq) => $sq->where('nombre_sede', 'like', "%{$request->search}%"))
                  ->orWhereHas('checklist', fn($sq) => $sq->where('title', 'like', "%{$request->search}%"));
            });
        }

        $inspections = $query->paginate(15)->withQueryString();

        return Inertia::render('Gestor/Inspections/Index', [
            'inspections' => $inspections,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Historial de inspecciones para el Técnico.
     */
    public function historyTecnico(Request $request)
    {
        $query = DynamicInspection::with(['checklist', 'sede.empresa'])
            ->where('user_id', Auth::id())
            ->completed()
            ->latest('completed_at');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->whereHas('sede', fn($sq) => $sq->where('nombre_sede', 'like', "%{$request->search}%"))
                  ->orWhereHas('checklist', fn($sq) => $sq->where('title', 'like', "%{$request->search}%"));
            });
        }

        $inspections = $query->paginate(15)->withQueryString();

        return Inertia::render('Tecnico/Inspections/History', [
            'inspections' => $inspections,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Ver detalle de una inspección.
     */
    public function show(DynamicInspection $inspection)
    {
        $inspection->load(['checklist.questions', 'user', 'sede.empresa', 'answers.question']);
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Determinar si es Gestor o Técnico para la vista
        $component = $user->isTechnician() 
            ? 'Tecnico/Inspections/Show' 
            : 'Gestor/Inspections/Show';

        return Inertia::render($component, [
            'inspection' => $inspection,
        ]);
    }
}
