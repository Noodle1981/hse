<?php

namespace App\Http\Controllers;

use App\Models\DynamicChecklist;
use App\Models\ChecklistAssignment;
use App\Models\DynamicInspection;
use App\Models\DynamicAnswer;
use App\Models\VisitCertificate;
use App\Services\VisitService;
use App\Models\Empresa;
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

        // Autogenerar asignación al vuelo si estamos dentro de una visita y es ad-hoc (sin asignación previa)
        if (!$assignmentId && $visitId) {
            $visit = \App\Models\VisitCertificate::find($visitId);
            if ($visit) {
                $assignment = \App\Models\ChecklistAssignment::create([
                    'user_id' => Auth::id(),
                    'sede_id' => $visit->sede_id,
                    'empresa_id' => $visit->empresa_id,
                    'dynamic_checklist_id' => $checklist->id,
                    'due_date' => now(),
                    'status' => 'in_progress',
                    'notes' => 'Vínculo automático al vuelo para visita #' . $visit->id,
                ]);
                $assignmentId = $assignment->id;
            }
        }

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

            $supervisorSignaturePath = $inspection->supervisor_signature;
            $technicianSignaturePath = $inspection->technician_signature;

            // Handle supervisor_signature
            if ($request->hasFile('supervisor_signature')) {
                $supervisorSignaturePath = $request->file('supervisor_signature')->store('signatures', 'public');
            } elseif ($request->filled('supervisor_signature')) {
                $data = $request->input('supervisor_signature');
                if (\Illuminate\Support\Str::startsWith($data, 'data:image')) {
                    $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $data);
                    $fileName = 'signatures/' . \Illuminate\Support\Str::random(40) . '.png';
                    \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, base64_decode($base64));
                    $supervisorSignaturePath = $fileName;
                } else {
                    $supervisorSignaturePath = $data;
                }
            }

            // Handle technician_signature
            if ($request->hasFile('technician_signature')) {
                $technicianSignaturePath = $request->file('technician_signature')->store('signatures', 'public');
            } elseif ($request->filled('technician_signature')) {
                $data = $request->input('technician_signature');
                if (\Illuminate\Support\Str::startsWith($data, 'data:image')) {
                    $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $data);
                    $fileName = 'signatures/' . \Illuminate\Support\Str::random(40) . '.png';
                    \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, base64_decode($base64));
                    $technicianSignaturePath = $fileName;
                } else {
                    $technicianSignaturePath = $data;
                }
            }

            $inspection->update([
                'status' => 'completed',
                'completed_at' => now(),
                'notes' => $request->notes,
                'supervisor_name' => $request->supervisor_name,
                'supervisor_signature' => $supervisorSignaturePath,
                'technician_signature' => $technicianSignaturePath,
                'result' => $inspection->evaluateResult(),
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

        if ($request->empresa) {
            $query->whereHas('sede', fn($q) => $q->where('empresa_id', $request->empresa));
        }
        if ($request->sede) {
            $query->where('sede_id', $request->sede);
        }
        if ($request->fecha_inicio) {
            $query->whereDate('completed_at', '>=', $request->fecha_inicio);
        }
        if ($request->fecha_fin) {
            $query->whereDate('completed_at', '<=', $request->fecha_fin);
        }

        $inspections = $query->paginate(15)->withQueryString();

        $user = Auth::user();
        $empresasQuery = Empresa::with(['sedes' => function ($q) {
            $q->orderBy('nombre_sede');
        }])->orderBy('nombre');

        if ($user->consultora_id) {
            $empresasQuery->where('consultora_id', $user->consultora_id);
        }
        $empresas = $empresasQuery->get();

        return Inertia::render('Gestor/Inspections/Index', [
            'inspections' => $inspections,
            'empresas' => $empresas,
            'filters' => $request->only(['empresa', 'sede', 'fecha_inicio', 'fecha_fin']),
        ]);
    }

    /**
     * Historial de inspecciones para el Técnico.
     */
    public function historyTecnico(Request $request)
    {
        $query = DynamicInspection::where('user_id', Auth::id())
            ->completed();

        if ($request->empresa) {
            $query->whereHas('sede', fn($q) => $q->where('empresa_id', $request->empresa));
        }
        if ($request->sede) {
            $query->where('sede_id', $request->sede);
        }
        if ($request->fecha_inicio) {
            $query->whereDate('completed_at', '>=', $request->fecha_inicio);
        }
        if ($request->fecha_fin) {
            $query->whereDate('completed_at', '<=', $request->fecha_fin);
        }
        if ($request->checklist_id) {
            $query->where('dynamic_checklist_id', $request->checklist_id);
        }

        // Obtener todas las combinaciones únicas de Fecha y Sede
        $allGroups = $query->select(
                DB::raw('DATE(completed_at) as completed_date'),
                'sede_id'
            )
            ->groupBy(DB::raw('DATE(completed_at)'), 'sede_id')
            ->orderBy(DB::raw('DATE(completed_at)'), 'desc')
            ->orderBy('sede_id', 'desc')
            ->get();

        // Paginación manual para grupos
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        $currentPageItems = $allGroups->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedGroups = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $allGroups->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()]
        );
        $paginatedGroups->appends($request->all());

        // Mapear y cargar relaciones para el sub-desglose
        $items = $currentPageItems->map(function ($group) use ($request) {
            $sede = \App\Models\Sede::with('empresa')->find($group->sede_id);
            
            // Buscar constancia de visita firmada/completada para ese día
            $visitCertificate = \App\Models\VisitCertificate::where('sede_id', $group->sede_id)
                ->whereDate('visit_date', $group->completed_date)
                ->first();

            // Obtener las inspecciones individuales de este grupo
            $inspectionsQuery = DynamicInspection::with('checklist')
                ->where('user_id', Auth::id())
                ->where('sede_id', $group->sede_id)
                ->whereDate('completed_at', $group->completed_date)
                ->completed();

            if ($request->checklist_id) {
                $inspectionsQuery->where('dynamic_checklist_id', $request->checklist_id);
            }

            $inspections = $inspectionsQuery->latest('completed_at')->get();

            $totalCount = $inspections->count();
            $successCount = $inspections->where('result', '!=', 'Fail')->count();
            $failedCount = $inspections->where('result', 'Fail')->count();
            $compliancePercentage = $totalCount > 0 ? round(($successCount / $totalCount) * 100) : 100;

            $checklistDetails = $inspections->map(function ($ins) {
                return [
                    'id' => $ins->id,
                    'checklist_title' => $ins->checklist?->title,
                    'registry_code' => $ins->checklist?->registry_code,
                    'completed_time' => $ins->completed_at ? $ins->completed_at->format('H:i') : '00:00',
                    'supervisor_name' => $ins->supervisor_name ?: 'Sin Firmar',
                    'result' => $ins->result,
                    'has_failed' => $ins->result === 'Fail',
                ];
            });

            return [
                'completed_date' => $group->completed_date,
                'completed_date_formatted' => \Carbon\Carbon::parse($group->completed_date)->format('d/m/Y'),
                'sede_id' => $group->sede_id,
                'sede_nombre' => $sede ? $sede->nombre_sede : 'Sede Desconocida',
                'empresa_nombre' => $sede && $sede->empresa ? $sede->empresa->nombre : 'Empresa Desconocida',
                'visit_certificate_id' => $visitCertificate ? $visitCertificate->id : null,
                'visit_certificate_status' => $visitCertificate ? $visitCertificate->status : null,
                'visit_certificate_code' => $visitCertificate ? $visitCertificate->code : null,
                'total_count' => $totalCount,
                'success_count' => $successCount,
                'failed_count' => $failedCount,
                'compliance_percentage' => $compliancePercentage,
                'checklists' => $checklistDetails,
            ];
        });

        $paginatedGroups->setCollection($items);

        $user = Auth::user();
        $empresasQuery = Empresa::with(['sedes' => function ($q) {
            $q->orderBy('nombre_sede');
        }])->orderBy('nombre');

        if ($user->consultora_id) {
            $empresasQuery->where('consultora_id', $user->consultora_id);
        }
        $empresas = $empresasQuery->get();

        return Inertia::render('Tecnico/Inspections/History', [
            'inspections' => $paginatedGroups,
            'empresas' => $empresas,
            'filters' => $request->only(['empresa', 'sede', 'fecha_inicio', 'fecha_fin', 'checklist_id']),
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
