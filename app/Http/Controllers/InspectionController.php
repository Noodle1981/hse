<?php

namespace App\Http\Controllers;

use App\Events\InspectionCompleted;
use App\Http\Requests\StoreChecklistCompletionRequest;
use App\Models\Asset;
use App\Models\AssetMetadataLog;
use App\Models\AssetType;
use App\Models\ChecklistAssignment;
use App\Models\DynamicAnswer;
use App\Models\DynamicChecklist;
use App\Models\DynamicInspection;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\User;
use App\Models\VisitCertificate;
use App\Models\VisitChecklistCompletion;
use App\Services\VisitService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
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
        $checklist = DynamicChecklist::with(['sections.questions'])->findOrFail($checklist_id);

        $assignmentId = $assignmentId ?: $request->query('assignment');
        $visitId = $request->query('visit_id');

        // Autogenerar asignación al vuelo si estamos dentro de una visita y es ad-hoc (sin asignación previa)
        if (! $assignmentId && $visitId) {
            $visit = VisitCertificate::find($visitId);
            if ($visit) {
                $assignment = ChecklistAssignment::create([
                    'user_id' => Auth::id(),
                    'sede_id' => $visit->sede_id,
                    'empresa_id' => $visit->empresa_id,
                    'dynamic_checklist_id' => $checklist->id,
                    'due_date' => now(),
                    'status' => 'in_progress',
                    'notes' => 'Vínculo automático al vuelo para visita #'.$visit->id,
                ]);
                $assignmentId = $assignment->id;
            }
        }

        $assignment = null;
        if ($assignmentId) {
            $assignment = ChecklistAssignment::where('id', $assignmentId)
                ->where(function ($q) {
                    $q->where('user_id', Auth::id())
                        ->orWhereNull('user_id');
                })
                ->firstOrFail();

            // Validar que el técnico tenga acceso a esta sede
            /** @var User $user */
            $user = Auth::user();
            if (! $user->isSuperAdmin() && ! $user->sedes()->where('sedes.id', $assignment->sede_id)->exists()) {
                abort(403, 'No tienes permiso para operar en esta sede.');
            }

            if ($assignment->status === 'completed') {
                $redirect = $visitId
                    ? route('tecnico.visits.in-progress', $visitId)
                    : route('tecnico.checklists.tasks.index');

                return redirect($redirect)->with('error', 'Esta asignación ya ha sido completada.');
            }

            $assignment->update([
                'status' => 'in_progress',
                'user_id' => Auth::id(), // Auto-asignar al técnico que la inicia
            ]);
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

        if (! $inspection) {
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

        $sedeId = $inspection->sede_id ?? ($assignment ? $assignment->sede_id : null);
        if (! $sedeId && $visitId) {
            $visit = VisitCertificate::find($visitId);
            $sedeId = $visit?->sede_id;
        }

        // Filtrar activos por los AssetTypes vinculados a este checklist.
        // Un AssetType está vinculado si alguna de sus reglas de mantenimiento
        // (en metadata_schema[].maintenance_rules[].checklist_resolucion_id) apunta a este checklist.
        $linkedAssetTypeIds = [];
        if ($sedeId) {
            $allAssetTypes = AssetType::all();
            foreach ($allAssetTypes as $assetType) {
                $schema = $assetType->metadata_schema ?? [];
                foreach ($schema as $field) {
                    foreach ($field['maintenance_rules'] ?? [] as $rule) {
                        if ((int) ($rule['checklist_resolucion_id'] ?? 0) === (int) $checklist->id) {
                            $linkedAssetTypeIds[] = $assetType->id;
                            break 2; // Ya encontramos un vínculo para este AssetType
                        }
                    }
                }
            }
        }

        if ($sedeId) {
            $assetQuery = Asset::where('sede_id', $sedeId)->with('assetType');
            // Si hay tipos de activo vinculados, filtrar; de lo contrario traer todos
            if (! empty($linkedAssetTypeIds)) {
                $assetQuery->whereIn('asset_type_id', $linkedAssetTypeIds);
            }
            $assets = $assetQuery->get();
        } else {
            $assets = [];
        }

        $inspectedAssetIds = [];
        if ($visitId) {
            $completedAssignmentIds = VisitChecklistCompletion::where('visit_certificate_id', $visitId)
                ->pluck('checklist_assignment_id')
                ->toArray();

            $inspectedAssetIds = DynamicInspection::whereIn('checklist_assignment_id', $completedAssignmentIds)
                ->where('status', 'completed')
                ->whereNotNull('asset_id')
                ->pluck('asset_id')
                ->toArray();
        }

        return Inertia::render('Tecnico/Inspections/Wizard', [
            'checklist' => $checklist,
            'assignment' => $assignment,
            'inspection' => $inspection,
            'visitId' => $visitId,
            'initialAnswers' => $answers,
            'assets' => $assets,
            'inspectedAssetIds' => $inspectedAssetIds,
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

            // Optimización: Eliminar respuestas previas y reinsertar en lote
            $inspection->answers()->delete();

            if (! empty($answers)) {
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

            $updateData = ['notes' => $request->input('notes')];
            if ($request->filled('asset_id')) {
                $assetId = $request->input('asset_id');
                $asset = Asset::with('assetType')->find($assetId);

                if ($asset && $asset->status === 'fuera_de_servicio') {
                    $schema = $asset->assetType?->metadata_schema ?? [];
                    $isResolvingChecklist = false;
                    foreach ($schema as $field) {
                        $rules = $field['maintenance_rules'] ?? [];
                        foreach ($rules as $rule) {
                            if (isset($rule['checklist_resolucion_id']) && (int) $rule['checklist_resolucion_id'] === (int) $inspection->dynamic_checklist_id) {
                                $isResolvingChecklist = true;
                                break 2;
                            }
                        }
                    }

                    if (! $isResolvingChecklist) {
                        throw ValidationException::withMessages([
                            'asset_id' => 'ACCESO DENEGADO: El equipo se encuentra Fuera de Servicio por Mantenimiento Vencido.',
                        ]);
                    }
                }
                $updateData['asset_id'] = $assetId;
            }
            $inspection->update($updateData);
        });

        return back()->with('message', 'Progreso sincronizado.');
    }

    public function complete(StoreChecklistCompletionRequest $request, $id, $inspection): RedirectResponse
    {
        $inspection = DynamicInspection::findOrFail($inspection);

        // Auditoría SRE: detectar intentos de manipulación de fechas
        $timeFields = ['completed_at', 'date', 'time', 'timestamp'];
        if ($request->hasAny($timeFields)) {
            $user = Auth::user();
            $attemptedPayload = $request->only($timeFields);
            Log::warning('[SRE SECURITY AUDIT] Posible intento de manipulación/fraude de horarios en checklist.', [
                'user_id' => $user ? $user->id : null,
                'user_name' => $user ? ($user->name ?? $user->nombres) : 'Invitado',
                'visit_id' => $request->input('visit_id'),
                'ip' => $request->ip(),
                'time_payload' => $attemptedPayload,
            ]);
        }

        // Sanitizar request para excluir campos de tiempo manipulados
        $safeData = $request->validatedWithSafeTime();

        DB::transaction(function () use ($request, $inspection, $safeData) {
            // Reutilizar lógica optimizada de guardado de respuestas
            $this->doSaveProgress($request, $inspection);

            $supervisorSignaturePath = $inspection->supervisor_signature;
            $technicianSignaturePath = $inspection->technician_signature;

            // Handle supervisor_signature
            if ($request->hasFile('supervisor_signature')) {
                $supervisorSignaturePath = $request->file('supervisor_signature')->store('signatures', 'public');
            } elseif ($request->filled('supervisor_signature')) {
                $data = $request->input('supervisor_signature');
                if (Str::startsWith($data, 'data:image')) {
                    $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $data);
                    $fileName = 'signatures/'.Str::random(40).'.png';
                    Storage::disk('public')->put($fileName, base64_decode($base64));
                    $supervisorSignaturePath = $fileName;
                } else {
                    $supervisorSignaturePath = $data;
                }
            }

            // Control de Seguridad SRE: Ignorar cualquier payload enviado desde el cliente.
            // La firma del técnico certificante se asocia de forma inmutable y directa desde el registro oficial en la DB.
            $technicianSignaturePath = Auth::user()->firma;

            $inspection->update([
                'status' => 'completed',
                'completed_at' => now(), // Timestamp inmutable del servidor
                'notes' => $safeData['notes'] ?? null,
                'supervisor_name' => $safeData['supervisor_name'],
                'supervisor_signature' => $supervisorSignaturePath,
                'technician_signature' => $technicianSignaturePath,
                'result' => $inspection->evaluateResult(),
            ]);

            InspectionCompleted::dispatch($inspection);

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

        /** @var User $user */
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
        $query = DynamicInspection::completed();

        if ($request->empresa) {
            $query->whereHas('sede', fn ($q) => $q->where('empresa_id', $request->empresa));
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
        if ($request->tecnico_id) {
            $query->where('user_id', $request->tecnico_id);
        }

        // Obtener todas las combinaciones únicas de Fecha, Sede y Técnico (user_id)
        $allGroups = $query->select(
            DB::raw('DATE(completed_at) as completed_date'),
            'sede_id',
            'user_id'
        )
            ->groupBy(DB::raw('DATE(completed_at)'), 'sede_id', 'user_id')
            ->orderBy(DB::raw('DATE(completed_at)'), 'desc')
            ->orderBy('sede_id', 'desc')
            ->orderBy('user_id', 'desc')
            ->get();

        // Paginación manual para grupos
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        $currentPageItems = $allGroups->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedGroups = new LengthAwarePaginator(
            $currentPageItems,
            $allGroups->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
        $paginatedGroups->appends($request->all());

        // Mapear y cargar relaciones para el sub-desglose
        $items = $currentPageItems->map(function ($group) {
            $sede = Sede::with('empresa')->find($group->sede_id);
            $techUser = User::find($group->user_id);

            // Buscar constancia de visita firmada/completada para ese día por ese técnico
            $visitCertificate = VisitCertificate::where('sede_id', $group->sede_id)
                ->where('user_id', $group->user_id)
                ->whereDate('visit_date', $group->completed_date)
                ->first();

            // Obtener las inspecciones individuales de este grupo
            $inspections = DynamicInspection::with('checklist')
                ->where('user_id', $group->user_id)
                ->where('sede_id', $group->sede_id)
                ->whereDate('completed_at', $group->completed_date)
                ->completed()
                ->latest('completed_at')
                ->get();

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
                'completed_date_formatted' => Carbon::parse($group->completed_date)->format('d/m/Y'),
                'sede_id' => $group->sede_id,
                'sede_nombre' => $sede ? $sede->nombre_sede : 'Sede Desconocida',
                'empresa_nombre' => $sede && $sede->empresa ? $sede->empresa->nombre : 'Empresa Desconocida',
                'visit_certificate_id' => $visitCertificate ? $visitCertificate->id : null,
                'visit_certificate_status' => $visitCertificate ? $visitCertificate->status : null,
                'visit_certificate_code' => $visitCertificate ? $visitCertificate->code : null,
                'tecnico_id' => $group->user_id,
                'tecnico_nombre' => $techUser ? $techUser->name : 'Técnico Desconocido',
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

        // Obtener listado de técnicos para el filtro
        $tecnicosQuery = User::where(function ($q) {
            $q->where('rol', 'tecnico')->orWhere('rol', 'Técnico');
        });
        if ($user->consultora_id) {
            $tecnicosQuery->where('consultora_id', $user->consultora_id);
        }
        $tecnicos = $tecnicosQuery->orderBy('name')->get();

        return Inertia::render('Gestor/Inspections/Index', [
            'inspections' => $paginatedGroups,
            'empresas' => $empresas,
            'tecnicos' => $tecnicos,
            'filters' => $request->only(['empresa', 'sede', 'fecha_inicio', 'fecha_fin', 'tecnico_id']),
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
            $query->whereHas('sede', fn ($q) => $q->where('empresa_id', $request->empresa));
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
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        $currentPageItems = $allGroups->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedGroups = new LengthAwarePaginator(
            $currentPageItems,
            $allGroups->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
        $paginatedGroups->appends($request->all());

        // Mapear y cargar relaciones para el sub-desglose
        $items = $currentPageItems->map(function ($group) use ($request) {
            $sede = Sede::with('empresa')->find($group->sede_id);

            // Buscar constancia de visita firmada/completada para ese día
            $visitCertificate = VisitCertificate::where('sede_id', $group->sede_id)
                ->whereDate('visit_date', $group->completed_date)
                ->first();

            // Obtener las inspecciones individuales de este grupo
            $inspectionsQuery = DynamicInspection::with(['checklist', 'asset'])
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
                    'asset' => $ins->asset ? [
                        'id' => $ins->asset->id,
                        'name' => $ins->asset->name,
                        'identifier' => $ins->asset->identifier,
                        'status' => $ins->asset->status,
                    ] : null,
                ];
            });

            return [
                'completed_date' => $group->completed_date,
                'completed_date_formatted' => Carbon::parse($group->completed_date)->format('d/m/Y'),
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
        $inspection->load(['checklist.sections.questions', 'checklist.questions', 'user', 'sede.empresa', 'answers.question']);

        /** @var User $user */
        $user = Auth::user();

        // Determinar si es Gestor o Técnico para la vista
        $component = $user->isTechnician()
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
        /** @var User $user */
        $user = Auth::user();
        if ($user->isTechnician()) {
            abort(403, 'Los técnicos no tienen permiso para re-vincular activos.');
        }

        $request->validate([
            'asset_id' => 'nullable|exists:assets,id',
        ]);

        $oldAsset = $inspection->asset;
        $newAssetId = $request->asset_id;

        DB::transaction(function () use ($inspection, $oldAsset, $newAssetId) {
            // 1. Actualizar el asset_id de la inspección
            $inspection->update(['asset_id' => $newAssetId]);

            // 2. Si tenía un activo anterior, restaurar sus métricas previas
            if ($oldAsset) {
                $logs = AssetMetadataLog::where('asset_id', $oldAsset->id)
                    ->where('dynamic_inspection_id', $inspection->id)
                    ->get();

                $oldMetadata = $oldAsset->metadata ?? [];
                foreach ($logs as $log) {
                    if ($log->old_value === null) {
                        unset($oldMetadata[$log->field_key]);
                    } else {
                        $decoded = json_decode($log->old_value, true);
                        $oldMetadata[$log->field_key] = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $log->old_value;
                    }
                    $log->delete();
                }
                $oldAsset->metadata = $oldMetadata;
                $oldAsset->save();
            }

            // 3. Si se vinculó a un nuevo activo, disparar el motor de sincronización de metadatos
            if ($newAssetId) {
                $newAsset = Asset::findOrFail($newAssetId);
                $newMetadata = $newAsset->metadata ?? [];

                $inspection->load('answers.question');

                foreach ($inspection->answers as $answer) {
                    $question = $answer->question;
                    if ($question && ! empty($question->vincular_a_metadata)) {
                        $metricKey = $question->vincular_a_metadata;
                        $val = $answer->value;

                        $oldVal = isset($newMetadata[$metricKey]) ? $newMetadata[$metricKey] : null;
                        $oldValStr = $oldVal !== null ? (is_array($oldVal) ? json_encode($oldVal) : (string) $oldVal) : null;
                        $newValStr = $val !== null ? (is_array($val) ? json_encode($val) : (string) $val) : null;

                        if ($oldValStr !== $newValStr) {
                            AssetMetadataLog::create([
                                'asset_id' => $newAsset->id,
                                'dynamic_inspection_id' => $inspection->id,
                                'field_key' => $metricKey,
                                'old_value' => $oldValStr,
                                'new_value' => $newValStr,
                                'changed_by' => Auth::id(),
                            ]);
                        }

                        $newMetadata[$metricKey] = $val;
                    }
                }

                $newAsset->metadata = $newMetadata;
                $newAsset->save();
            }

            // 4. Re-evaluar alertas del sistema para que ambos activos actualicen sus estados SRE
            Artisan::call('assets:check-alerts');
        });

        return back()->with('message', 'Activo re-vinculado con éxito y alertas actualizadas.');
    }
}
