<?php

namespace App\Http\Controllers;

use App\Models\AnnualPlan;
use App\Models\ChecklistAssignment;
use App\Models\DynamicInspection;
use App\Models\Empresa;
use App\Models\Hallazgo;
use App\Models\Incidente;
use App\Models\MasterReport;
use App\Models\Sede;
use App\Models\Training;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Consultora;
use Inertia\Inertia;

class AdministrationController extends Controller
{
    /**
     * Show the administration hub.
     */
    public function hub()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $consultoraName = $user->consultora?->nombre ?? 'XAMASAFE';

        return Inertia::render('Gestor/AdminHub', [
            'consultoraName' => $consultoraName,
        ]);
    }

    /**
     * Show the user manager.
     */
    public function users(Request $request)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        $query = User::query();

        if (! $currentUser->isSuperAdmin()) {
            $query->where('consultora_id', $currentUser->consultora_id);
        }

        if ($request->boolean('showDeleted')) {
            $query->onlyTrashed();
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nombres', 'like', "%{$request->search}%")
                    ->orWhere('apellidos', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('matricula', 'like', "%{$request->search}%");
            });
        }

        if ($request->roleFilter && $request->roleFilter !== 'all') {
            $query->where('rol', $request->roleFilter);
        }

        $users = $query->orderBy('apellidos')->orderBy('nombres')->paginate(15)->withQueryString();

        return Inertia::render('Gestor/Users', [
            'users' => $users,
            'filters' => $request->only(['search', 'roleFilter', 'showDeleted']),
        ]);
    }

    /**
     * Show the technician manager.
     */
    public function technicians(Request $request)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        $query = User::query()->where('rol', User::ROLE_TECNICO);

        if (! $currentUser->isSuperAdmin()) {
            $query->where('consultora_id', $currentUser->consultora_id);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nombres', 'like', "%{$request->search}%")
                    ->orWhere('apellidos', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('matricula', 'like', "%{$request->search}%");
            });
        }

        $technicians = $query->orderBy('nombres')
            ->paginate(10)
            ->withQueryString();

        // Data for assignment modal
        $companies = Empresa::orderBy('nombre')->get();

        return Inertia::render('Gestor/Technicians', [
            'technicians' => $technicians,
            'companies' => $companies,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Get sites for a specific company (for the assignment modal).
     */
    public function getCompanySites(Empresa $empresa)
    {
        return response()->json($empresa->sedes()->orderBy('nombre_sede')->get());
    }

    /**
     * Get current assignments for a technician.
     */
    public function getTechnicianAssignments(User $user)
    {
        $this->ensureTenancy($user);

        return response()->json($user->sedes()->with('empresa')->get());
    }

    /**
     * Assign a site to a technician.
     */
    public function assignSede(Request $request, User $user)
    {
        $this->ensureTenancy($user);

        $request->validate([
            'sede_id' => 'required|exists:sedes,id',
        ]);

        if ($user->sedes()->where('sedes.id', $request->sede_id)->exists()) {
            return back()->with('error', 'El técnico ya está asignado a esta sede.');
        }

        $user->sedes()->attach($request->sede_id);

        return back()->with('message', 'Sede asignada correctamente.');
    }

    /**
     * Remove assignment.
     */
    public function removeAssignment(Request $request, User $user)
    {
        $this->ensureTenancy($user);

        $request->validate([
            'sede_id' => 'required|exists:sedes,id',
        ]);

        $user->sedes()->detach($request->sede_id);

        return back()->with('message', 'Asignación eliminada correctamente.');
    }

    /**
     * Store or Update user.
     */
    public function saveUser(Request $request)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        $editingId = $request->input('id');

        // Normalizar campos para asegurar unicidad case-insensitive
        if ($request->has('email')) {
            $request->merge(['email' => strtolower(trim($request->email))]);
        }
        if ($request->has('matricula')) {
            $request->merge(['matricula' => strtoupper(trim($request->matricula))]);
        }
        if ($request->has('n_doc')) {
            $request->merge(['n_doc' => $request->n_doc ? strtoupper(trim($request->n_doc)) : null]);
        }
        if ($request->has('telefono')) {
            $request->merge(['telefono' => trim($request->telefono)]);
        }

        $rules = [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($editingId)],
            'rol' => 'required|in:gestor,tecnico',
            'matricula' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($editingId)],
            'telefono' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($editingId)],
            'n_doc' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($editingId)],
            'estado' => 'required|in:activo,inactivo',
        ];

        if (! $editingId || $request->password) {
            $rules['password'] = 'required|string|min:6';
        }

        $messages = [
            'nombres.required' => 'El nombre es obligatorio.',
            'apellidos.required' => 'El apellido es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'rol.required' => 'El rol es obligatorio.',
            'rol.in' => 'El rol seleccionado no es válido.',
            'matricula.required' => 'La matrícula es obligatoria.',
            'matricula.unique' => 'Esta matrícula ya está registrada.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.unique' => 'Este teléfono ya está registrado.',
            'n_doc.unique' => 'Este número de documento ya está registrado.',
            'estado.required' => 'El estado es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ];

        $request->validate($rules, $messages);

        $consultoraId = !$currentUser->isSuperAdmin() ? $currentUser->consultora_id : ($request->input('consultora_id') ?? ($editingId ? User::find($editingId)?->consultora_id : null));

        if ($consultoraId && $request->input('estado') === 'activo') {
            $consultora = Consultora::findOrFail($consultoraId);
            $rol = $request->input('rol');

            if ($rol === 'tecnico') {
                $count = User::where('consultora_id', $consultoraId)
                    ->whereIn('rol', ['tecnico', 'technician'])
                    ->where('estado', 'activo')
                    ->when($editingId, function ($q) use ($editingId) {
                        $q->where('id', '!=', $editingId);
                    })
                    ->count();
                if ($count >= $consultora->max_tecnicos) {
                    return back()->withErrors(['rol' => 'Se ha alcanzado el límite de técnicos activos (' . $consultora->max_tecnicos . ') para esta consultora.']);
                }
            } elseif ($rol === 'gestor') {
                $count = User::where('consultora_id', $consultoraId)
                    ->whereIn('rol', ['gestor', 'administrador'])
                    ->where('estado', 'activo')
                    ->when($editingId, function ($q) use ($editingId) {
                        $q->where('id', '!=', $editingId);
                    })
                    ->count();
                if ($count >= $consultora->max_gestores) {
                    return back()->withErrors(['rol' => 'Se ha alcanzado el límite de gestores activos (' . $consultora->max_gestores . ') para esta consultora.']);
                }
            }
        }

        $data = $request->only(['nombres', 'apellidos', 'email', 'rol', 'matricula', 'telefono', 'n_doc', 'estado']);

        if (array_key_exists('n_doc', $data) && trim($data['n_doc'] ?? '') === '') {
            $data['n_doc'] = null;
        }

        if (! $currentUser->isSuperAdmin()) {
            $data['consultora_id'] = $currentUser->consultora_id;
        }

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
            $data['must_change_password'] = true;
        }

        if ($editingId) {
            $user = User::findOrFail($editingId);
            $this->ensureTenancy($user);
            $user->update($data);
            $message = 'Usuario actualizado exitosamente.';
        } else {
            User::create($data);
            $message = 'Usuario creado exitosamente.';
        }

        return back()->with('message', $message);
    }

    /**
     * Delete user (soft delete).
     */
    public function deleteUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $this->ensureTenancy($user);

        $user->update(['estado' => 'inactivo']);
        $user->delete();

        return back()->with('message', 'Usuario dado de baja exitosamente.');
    }

    public function restoreUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $this->ensureTenancy($user);

        if ($user->consultora_id) {
            $consultora = Consultora::findOrFail($user->consultora_id);
            if ($user->rol === 'tecnico' || $user->rol === 'technician') {
                $activeCount = User::where('consultora_id', $user->consultora_id)
                    ->whereIn('rol', ['tecnico', 'technician'])
                    ->where('estado', 'activo')
                    ->count();
                if ($activeCount >= $consultora->max_tecnicos) {
                    return back()->with('error', 'No se puede restaurar el usuario: Límite de técnicos excedido.');
                }
            } elseif ($user->rol === 'gestor' || $user->rol === 'administrador') {
                $activeCount = User::where('consultora_id', $user->consultora_id)
                    ->whereIn('rol', ['gestor', 'administrador'])
                    ->where('estado', 'activo')
                    ->count();
                if ($activeCount >= $consultora->max_gestores) {
                    return back()->with('error', 'No se puede restaurar el usuario: Límite de gestores excedido.');
                }
            }
        }

        $user->restore();
        $user->update(['estado' => 'activo']);

        return back()->with('message', 'Usuario restaurado exitosamente.');
    }

    /**
     * Show the super admin dashboard.
     */
    public function dashboard(Request $request)
    {
        $this->ensureSuperAdminOrVendedor();

        $from = $request->query('from', now()->subMonths(1)->startOfMonth()->format('Y-m-d'));
        $to = $request->query('to', now()->endOfMonth()->format('Y-m-d'));
        $consultoraId = $request->query('consultora_id', 'all');

        // Obtener listado de consultoras para el filtro
        $consultants = Consultora::orderBy('nombre')->get(['id', 'nombre']);

        if ($consultoraId && $consultoraId !== 'all') {
            $consultora = Consultora::findOrFail($consultoraId);
            $consultoraName = $consultora->nombre;

            $empresasCount = $consultora->empresas()->count();
            $gestoresCount = User::where('consultora_id', $consultora->id)->where('rol', 'gestor')->count();
            $tecnicosCount = User::where('consultora_id', $consultora->id)->where('rol', 'tecnico')->count();
            
            $inspectionsCount = DynamicInspection::whereHas('sede.empresa', function ($q) use ($consultora) {
                $q->where('consultora_id', $consultora->id);
            })->whereBetween('dynamic_inspections.completed_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
              ->where('dynamic_inspections.status', 'completed')->count();

            $trainingsCount = Training::whereHas('empresa', function ($q) use ($consultora) {
                $q->where('consultora_id', $consultora->id);
            })->whereBetween('trainings.date', [$from . ' 00:00:00', $to . ' 23:59:59'])
              ->where('trainings.status', 'completed')->count();

            $masterReportsCount = MasterReport::whereHas('sede.empresa', function ($q) use ($consultora) {
                $q->where('consultora_id', $consultora->id);
            })->whereBetween('master_reports.created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
              ->whereIn('master_reports.estado', ['finalizado', 'aprobado'])->count();

            $companies = Empresa::where('consultora_id', $consultora->id)
                ->with('consultora')
                ->withCount([
                    'sedes',
                    'inspecciones' => function ($q) use ($from, $to) {
                        $q->whereBetween('dynamic_inspections.completed_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                          ->where('dynamic_inspections.status', 'completed');
                    },
                    'trainings' => function ($q) use ($from, $to) {
                        $q->whereBetween('trainings.date', [$from . ' 00:00:00', $to . ' 23:59:59'])
                          ->where('trainings.status', 'completed');
                    },
                    'informes' => function ($q) use ($from, $to) {
                        $q->whereBetween('master_reports.created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                          ->whereIn('master_reports.estado', ['finalizado', 'aprobado']);
                    }
                ])
                ->orderBy('nombre')
                ->get();
        } else {
            $consultoraName = 'Todas las Consultoras';

            $empresasCount = Empresa::count();
            $gestoresCount = User::where('rol', 'gestor')->count();
            $tecnicosCount = User::where('rol', 'tecnico')->count();

            $inspectionsCount = DynamicInspection::whereBetween('dynamic_inspections.completed_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
              ->where('dynamic_inspections.status', 'completed')->count();

            $trainingsCount = Training::whereBetween('trainings.date', [$from . ' 00:00:00', $to . ' 23:59:59'])
              ->where('trainings.status', 'completed')->count();

            $masterReportsCount = MasterReport::whereBetween('master_reports.created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
              ->whereIn('master_reports.estado', ['finalizado', 'aprobado'])->count();

            $companies = Empresa::with('consultora')
                ->withCount([
                    'sedes',
                    'inspecciones' => function ($q) use ($from, $to) {
                        $q->whereBetween('dynamic_inspections.completed_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                          ->where('dynamic_inspections.status', 'completed');
                    },
                    'trainings' => function ($q) use ($from, $to) {
                        $q->whereBetween('trainings.date', [$from . ' 00:00:00', $to . ' 23:59:59'])
                          ->where('trainings.status', 'completed');
                    },
                    'informes' => function ($q) use ($from, $to) {
                        $q->whereBetween('master_reports.created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                          ->whereIn('master_reports.estado', ['finalizado', 'aprobado']);
                    }
                ])
                ->orderBy('nombre')
                ->get();
        }

        $checklistsTemplatesCount = \App\Models\DynamicChecklist::count();

        $kpis = [
            'empresas_count' => $empresasCount,
            'gestores_count' => $gestoresCount,
            'tecnicos_count' => $tecnicosCount,
            'checklists_templates_count' => $checklistsTemplatesCount,
            'inspections_count' => $inspectionsCount,
            'trainings_count' => $trainingsCount,
            'master_reports_count' => $masterReportsCount,
        ];

        return Inertia::render('Admin/Dashboard', [
            'consultoraName' => $consultoraName,
            'consultants' => $consultants,
            'kpis' => $kpis,
            'companies' => $companies,
            'filters' => [
                'from' => $from,
                'to' => $to,
                'consultora_id' => $consultoraId,
            ],
        ]);
    }

    /**
     * Show the gestor dashboard.
     */
    public function gestorDashboard(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $consultoraId = $user->consultora_id;

        $fromRaw = $request->query('from', now()->startOfMonth()->format('Y-m-d'));
        $toRaw = $request->query('to', now()->endOfMonth()->format('Y-m-d'));

        $from = \Carbon\Carbon::parse($fromRaw)->startOfDay();
        $to = \Carbon\Carbon::parse($toRaw)->endOfDay();

        // global KPIs (Filtered by consultora)
        $kpis = [
            'total_companies' => Empresa::where('consultora_id', $consultoraId)->count(),
            'total_inspections' => DynamicInspection::whereBetween('completed_at', [$from, $to])
                ->whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $consultoraId))
                ->completed()
                ->count(),
            'total_findings' => Hallazgo::whereBetween('fecha_deteccion', [$from, $to])
                ->whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $consultoraId))
                ->count(),
            'total_incidents' => Incidente::whereBetween('fecha_ocurrencia', [$from, $to])
                ->whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $consultoraId))
                ->count(),
            'total_reports' => MasterReport::whereBetween('updated_at', [$from, $to])
                ->whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $consultoraId))
                ->whereIn('estado', ['finalizado', 'aprobado'])
                ->count(),
        ];

        // Intelligence Matrix: Companies with metrics
        $companies = Empresa::where('consultora_id', $consultoraId)
            ->withCount([
                'inspecciones' => fn ($q) => $q->whereBetween('dynamic_inspections.completed_at', [$from, $to])->completed(),
                'hallazgos' => fn ($q) => $q->whereBetween('hallazgos.fecha_deteccion', [$from, $to]),
                'incidentes' => fn ($q) => $q->whereBetween('incidentes.fecha_ocurrencia', [$from, $to]),
                'informes' => fn ($q) => $q->whereBetween('master_reports.updated_at', [$from, $to])->whereIn('master_reports.estado', ['finalizado', 'aprobado']),
                'checklistAssignments as active_assignments_count',
                'sedes as sedes_con_plan_count' => fn ($q) => $q->whereHas('annualPlans', fn ($p) => $p->where('year', now()->year)),
            ])
            ->orderBy('nombre')
            ->get();

        // Efficiency (Plan vs Done)
        $efficiency = $this->calculateConsultoraEfficiency($consultoraId, $from);

        // Tactical Terminal (Unified Activity Log)
        $recentEvents = $this->getTacticalTerminalEvents($consultoraId);

        // Audit de horas (Campo vs Gabinete y alertas de sobrejornada)
        $timeService = new \App\Services\TimeTrackingService;
        $visits = \App\Models\VisitCertificate::with('sede')
            ->whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $consultoraId))
            ->whereBetween('visit_date', [$from, $to])
            ->whereIn('status', ['completed', 'signed'])
            ->get();

        $totalFieldHours = 0.0;
        $totalOfficeHours = 0.0;
        $exceededWarningsCount = 0;

        foreach ($visits as $visit) {
            $fieldHours = $timeService->calculateFieldHours($visit);
            $totalFieldHours += $fieldHours;
            $totalOfficeHours += (float) $visit->horas_gabinete_estimadas;

            // Alerta si el checkin ocurrió fuera del horario de la sede (siempre que no sea roster 24/7)
            if ($visit->start_time && ! $timeService->verifyWorkingHours($visit->sede, $visit->start_time)) {
                $exceededWarningsCount++;
            } else {
                // Cálculo dinámico inteligente de sobrejornada:
                // Si las horas de campo exceden la duración de la jornada operativa de la sede
                $sede = $visit->sede;
                if ($sede && ! $sede->es_roster_24_7 && $sede->hora_apertura && $sede->hora_cierre) {
                    try {
                        $apertura = \Carbon\Carbon::createFromFormat('H:i:s', $sede->hora_apertura);
                    } catch (\Exception $e) {
                        try {
                            $apertura = \Carbon\Carbon::createFromFormat('H:i', $sede->hora_apertura);
                        } catch (\Exception $e2) {
                            $apertura = null;
                        }
                    }

                    try {
                        $cierre = \Carbon\Carbon::createFromFormat('H:i:s', $sede->hora_cierre);
                    } catch (\Exception $e) {
                        try {
                            $cierre = \Carbon\Carbon::createFromFormat('H:i', $sede->hora_cierre);
                        } catch (\Exception $e2) {
                            $cierre = null;
                        }
                    }

                    if ($apertura && $cierre) {
                        $maxHours = $cierre->diffInSeconds($apertura) / 3600.0;
                        if ($fieldHours > $maxHours) {
                            $exceededWarningsCount++;
                        }
                    }
                }
            }
        }

        $hoursTracking = [
            'field_hours' => round($totalFieldHours, 2),
            'office_hours' => round($totalOfficeHours, 2),
            'exceeded_warnings_count' => $exceededWarningsCount,
        ];

        return Inertia::render('Gestor/Dashboard', [
            'kpis' => $kpis,
            'companies' => $companies,
            'efficiency' => $efficiency,
            'recentEvents' => $recentEvents,
            'hoursTracking' => $hoursTracking,
            'filters' => [
                'from' => $fromRaw,
                'to' => $toRaw,
            ],
        ]);
    }

    private function getTacticalTerminalEvents($consultoraId)
    {
        $limit = 15;

        // 1. Inspections
        $inspections = DynamicInspection::with(['checklist', 'user', 'sede.empresa'])
            ->whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $consultoraId))
            ->completed()
            ->latest('completed_at')
            ->limit($limit)
            ->get()
            ->map(fn ($i) => [
                'time' => $i->completed_at,
                'label' => 'INSPECCIÓN',
                'color' => 'emerald',
                'description' => "{$i->user?->full_name} finalizó '{$i->checklist?->title}' en ".($i->sede?->empresa?->nombre ?? 'Sede desconocida'),
                'diffText' => $i->completed_at?->diffForHumans(),
            ]);

        // 2. Hallazgos
        $findings = Hallazgo::with(['sede.empresa'])
            ->whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $consultoraId))
            ->latest('fecha_deteccion')
            ->limit($limit)
            ->get()
            ->map(fn ($h) => [
                'time' => $h->created_at,
                'label' => 'HALLAZGO',
                'color' => 'yellow',
                'description' => "Nuevo hallazgo '{$h->titulo}' detectado en ".($h->sede?->empresa?->nombre ?? 'Sede desconocida')." (Nivel: {$h->criticidad})",
                'diffText' => $h->created_at?->diffForHumans(),
            ]);

        // 3. Incidentes
        $incidents = Incidente::with(['sede.empresa'])
            ->whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $consultoraId))
            ->latest('fecha_ocurrencia')
            ->limit($limit)
            ->get()
            ->map(fn ($inc) => [
                'time' => $inc->created_at,
                'label' => 'SINIESTRO',
                'color' => 'red',
                'description' => 'Siniestro reportado en '.($inc->sede?->empresa?->nombre ?? 'Sede desconocida').": {$inc->tipo}",
                'diffText' => $inc->created_at?->diffForHumans(),
            ]);

        // 4. Reportes
        $reports = MasterReport::with(['sede.empresa'])
            ->whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $consultoraId))
            ->whereIn('estado', ['finalizado', 'aprobado'])
            ->latest('updated_at')
            ->limit($limit)
            ->get()
            ->map(fn ($r) => [
                'time' => $r->updated_at,
                'label' => 'REPORTE',
                'color' => 'blue',
                'description' => 'Informe Maestro consolidado para '.($r->sede?->empresa?->nombre ?? 'Sede desconocida')." ({$r->periodo})",
                'diffText' => $r->updated_at?->diffForHumans(),
            ]);

        return collect([])
            ->concat($inspections)
            ->concat($findings)
            ->concat($incidents)
            ->concat($reports)
            ->sortByDesc('time')
            ->take(20)
            ->values();
    }

    private function calculateConsultoraEfficiency($consultoraId, $from = null)
    {
        $date = $from ?? now();
        $year = $date->year;
        $month = $date->month;

        $totalPlanned = AnnualPlan::whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $consultoraId))
            ->where('scheduled_months', 'like', "%{$year}-".str_pad($month, 2, '0', STR_PAD_LEFT).'%')
            ->count();

        $totalDone = DynamicInspection::whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $consultoraId))
            ->whereYear('completed_at', $year)
            ->whereMonth('completed_at', $month)
            ->completed()
            ->count();

        return [
            'percentage' => $totalPlanned > 0 ? round(($totalDone / $totalPlanned) * 100) : 0,
            'planned' => $totalPlanned,
            'done' => $totalDone,
        ];
    }

    private function getConsultoraActivity($consultoraId)
    {
        return DynamicInspection::with(['checklist', 'user', 'sede.empresa'])
            ->whereHas('user', fn ($q) => $q->where('consultora_id', $consultoraId))
            ->completed()
            ->latest('completed_at')
            ->limit(10)
            ->get()
            ->map(fn ($i) => [
                'type' => 'inspection',
                'title' => 'Cierre de Inspección',
                'description' => $i->checklist->title ?? 'N/A',
                'user' => $i->user->full_name,
                'company' => $i->sede->empresa->nombre ?? 'N/A',
                'date' => $i->completed_at->diffForHumans(),
            ]);
    }

    private function getRecentActivity()
    {
        $activities = collect();

        // Insps
        $insps = DynamicInspection::with(['checklist', 'user', 'sede.empresa'])
            ->completed()
            ->latest('completed_at')
            ->limit(10)
            ->get()
            ->map(fn ($i) => [
                'type' => 'inspection',
                'title' => 'Inspección Finalizada',
                'description' => $i->checklist->title ?? 'General',
                'user' => $i->user->full_name ?? 'Sistema',
                'company' => $i->sede->empresa->nombre ?? 'N/A',
                'date' => $i->completed_at->diffForHumans(),
            ]);
        $activities = $activities->concat($insps);

        // Incidentes
        $incs = Incidente::with(['sede.empresa'])
            ->latest('fecha_ocurrencia')
            ->limit(5)
            ->get()
            ->map(fn ($i) => [
                'type' => 'incident',
                'title' => 'Incidente Reportado',
                'description' => $i->descripcion,
                'user' => 'Alerta Siniestralidad',
                'company' => $i->sede->empresa->nombre ?? 'N/A',
                'date' => $i->fecha_ocurrencia->diffForHumans(),
            ]);
        $activities = $activities->concat($incs);

        return $activities->sortByDesc('date')->values();
    }

    private function ensureSuperAdmin()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (! $user->isSuperAdmin()) {
            abort(403, 'Acceso restringido a Super Administradores.');
        }
    }

    private function ensureSuperAdminOrVendedor()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (! ($user->isSuperAdmin() || $user->isVendedor())) {
            abort(403, 'Acceso restringido a Súper Administradores o Vendedores.');
        }
    }

    private function ensureTenancy(User $user)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        if (! $currentUser->isSuperAdmin() && $user->consultora_id !== $currentUser->consultora_id) {
            abort(403, 'Acceso no autorizado.');
        }
    }

    /**
     * List all consultoras (paginated).
     */
    public function consultantsIndex()
    {
        $this->ensureSuperAdminOrVendedor();
        $consultants = Consultora::with(['suscripcionLogs' => function ($query) {
            $query->whereNull('estado_anterior')->with('user');
        }])->withCount([
            'users as tecnicos_count' => function ($query) {
                $query->whereIn('rol', ['tecnico', 'technician'])->where('estado', 'activo');
            },
            'users as gestores_count' => function ($query) {
                $query->whereIn('rol', ['gestor', 'administrador'])->where('estado', 'activo');
            },
            'empresas'
        ])->paginate(10);

        return Inertia::render('Admin/Consultants/Index', [
            'consultants' => $consultants
        ]);
    }

    /**
     * Show form to create a consultora.
     */
    public function consultantsCreate()
    {
        $this->ensureSuperAdminOrVendedor();
        return Inertia::render('Admin/Consultants/Edit', [
            'consultant' => null
        ]);
    }

    /**
     * Store a new consultora.
     */
    public function consultantsStore(Request $request)
    {
        $this->ensureSuperAdminOrVendedor();
        $request->validate([
            'nombre' => 'required|string|max:255',
            'cuit' => 'required|string|max:20|unique:consultoras,cuit',
            'max_tecnicos' => 'required|integer|min:1',
            'max_gestores' => 'required|integer|min:1',
            'max_empresas' => 'required|integer|min:1',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'fiscal_address' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:100',
            'locality' => 'nullable|string|max:100',
            'estado_suscripcion' => 'required|string|in:activa,morosa,baja',
            'color_primary' => 'required|string|size:7',
            'color_secondary' => 'required|string|size:7',
            'color_accent' => 'required|string|size:7',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        $data = $request->only([
            'nombre', 'cuit', 'max_tecnicos', 'max_gestores', 'max_empresas', 'email', 'phone', 
            'description', 'fiscal_address', 'province', 'locality',
            'estado_suscripcion', 'color_primary', 'color_secondary', 'color_accent'
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos/consultoras', 'public');
            $data['logo_path'] = $path;
        }

        Consultora::create($data);

        return redirect()->route('admin.consultants.index')->with('message', 'Consultora creada exitosamente.');
    }

    /**
     * Edit a consultora.
     */
    public function consultantsEdit(Consultora $consultant)
    {
        $this->ensureSuperAdminOrVendedor();

        $logs = $consultant->suscripcionLogs()
            ->with('user:id,nombres,apellidos,email')
            ->orderBy('created_at', 'desc')
            ->get();

        $formattedLogs = [];
        $count = count($logs);
        for ($i = 0; $i < $count; $i++) {
            $current = $logs[$i];
            $startDate = $current->created_at;
            $endDate = ($i > 0) ? $logs[$i - 1]->created_at : now();
            
            $duration = $startDate->diffForHumans($endDate, [
                'parts' => 2,
                'join' => true,
                'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE
            ]);

            $formattedLogs[] = [
                'id' => $current->id,
                'estado_anterior' => $current->estado_anterior,
                'estado_nuevo' => $current->estado_nuevo,
                'created_at' => $current->created_at ? $current->created_at->toISOString() : null,
                'created_at_formatted' => $current->created_at ? $current->created_at->format('d/m/Y H:i') : 'N/A',
                'user' => $current->user ? [
                    'name' => $current->user->nombres . ' ' . $current->user->apellidos,
                    'email' => $current->user->email,
                ] : null,
                'duration' => $duration,
                'is_current' => ($i == 0),
            ];
        }

        if ($count === 0) {
            $createdAt = $consultant->created_at ?? now();
            $duration = $createdAt->diffForHumans(now(), [
                'parts' => 2,
                'join' => true,
                'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE
            ]);
            $formattedLogs[] = [
                'id' => 0,
                'estado_anterior' => null,
                'estado_nuevo' => $consultant->estado_suscripcion ?? 'activa',
                'created_at' => $createdAt->toISOString(),
                'created_at_formatted' => $createdAt->format('d/m/Y H:i'),
                'user' => null,
                'duration' => $duration,
                'is_current' => true,
            ];
        }

        // Recuperar y formatear los audit_logs
        $auditLogs = \App\Models\AuditLog::where('target_type', Consultora::class)
            ->where('target_id', $consultant->id)
            ->with('user:id,nombres,apellidos,email,rol')
            ->orderBy('created_at', 'desc')
            ->get();

        $formattedAuditLogs = [];
        $auditCount = count($auditLogs);
        for ($i = 0; $i < $auditCount; $i++) {
            $current = $auditLogs[$i];
            $startDate = $current->created_at;
            $endDate = ($i > 0) ? $auditLogs[$i - 1]->created_at : now();
            
            $duration = $startDate ? $startDate->diffForHumans($endDate, [
                'parts' => 2,
                'join' => true,
                'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE
            ]) : 'N/A';

            $formattedAuditLogs[] = [
                'id' => $current->id,
                'action' => $current->action,
                'payload' => $current->payload,
                'created_at' => $current->created_at ? $current->created_at->toISOString() : null,
                'created_at_formatted' => $current->created_at ? $current->created_at->format('d/m/Y H:i') : 'N/A',
                'user' => $current->user ? [
                    'name' => $current->user->nombres . ' ' . $current->user->apellidos,
                    'email' => $current->user->email,
                    'rol' => $current->user->rol,
                ] : null,
                'duration' => $duration,
                'is_current' => ($i == 0),
            ];
        }

        return Inertia::render('Admin/Consultants/Edit', [
            'consultant' => [
                'id' => $consultant->id,
                'nombre' => $consultant->nombre,
                'cuit' => $consultant->cuit,
                'max_tecnicos' => $consultant->max_tecnicos,
                'max_gestores' => $consultant->max_gestores ?? 2,
                'max_empresas' => $consultant->max_empresas ?? 5,
                'description' => $consultant->description,
                'fiscal_address' => $consultant->fiscal_address,
                'province' => $consultant->province,
                'locality' => $consultant->locality,
                'estado_suscripcion' => $consultant->estado_suscripcion ?? 'activa',
                'email' => $consultant->email,
                'phone' => $consultant->phone,
                'color_primary' => $consultant->color_primary ?? '#1e40af',
                'color_secondary' => $consultant->color_secondary ?? '#6b7280',
                'color_accent' => $consultant->color_accent ?? '#16a34a',
                'logo_path' => $consultant->logo_path ? asset('storage/' . $consultant->logo_path) : null,
            ],
            'suscripcion_logs' => $formattedLogs,
            'audit_logs' => $formattedAuditLogs
        ]);
    }

    /**
     * Update a consultora.
     */
    public function consultantsUpdate(Request $request, Consultora $consultant)
    {
        $this->ensureSuperAdminOrVendedor();
        $request->validate([
            'nombre' => 'required|string|max:255',
            'cuit' => 'required|string|max:20|unique:consultoras,cuit,' . $consultant->id,
            'max_tecnicos' => 'required|integer|min:1',
            'max_gestores' => 'required|integer|min:1',
            'max_empresas' => 'required|integer|min:1',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'fiscal_address' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:100',
            'locality' => 'nullable|string|max:100',
            'estado_suscripcion' => 'required|string|in:activa,morosa,baja',
            'color_primary' => 'required|string|size:7',
            'color_secondary' => 'required|string|size:7',
            'color_accent' => 'required|string|size:7',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        $data = $request->only([
            'nombre', 'cuit', 'max_tecnicos', 'max_gestores', 'max_empresas', 'email', 'phone', 
            'description', 'fiscal_address', 'province', 'locality',
            'estado_suscripcion', 'color_primary', 'color_secondary', 'color_accent'
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos/consultoras', 'public');
            $data['logo_path'] = $path;
        }

        $consultant->update($data);

        return redirect()->route('admin.consultants.index')->with('message', 'Consultora actualizada exitosamente.');
    }

    /**
     * Destroy a consultora.
     */
    public function consultantsDestroy(Consultora $consultant)
    {
        $this->ensureSuperAdmin();
        
        // Prevent deleting the main consultora if it's the only one or linked to active users
        if ($consultant->users()->count() > 0) {
            return back()->with('error', 'No se puede eliminar una consultora con usuarios activos.');
        }

        $consultant->delete();

        return redirect()->route('admin.consultants.index')->with('message', 'Consultora eliminada exitosamente.');
    }

    /**
     * List all users of a consultora.
     */
    public function consultantUsersIndex(Consultora $consultant)
    {
        $this->ensureSuperAdmin();
        $users = $consultant->users()->get();

        return Inertia::render('Admin/Consultants/Users', [
            'consultant' => $consultant,
            'users' => $users
        ]);
    }

    /**
     * Create or update a user for a consultora.
     */
    public function consultantUsersSave(Request $request, Consultora $consultant)
    {
        $this->ensureSuperAdmin();
        $userId = $request->input('id');

        $rules = [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $userId,
            'rol' => 'required|string|in:gestor,tecnico',
            'estado' => 'required|string|in:activo,inactivo',
        ];

        if (!$userId) {
            $rules['password'] = 'required|string|min:8';
        } else {
            $rules['password'] = 'nullable|string|min:8';
        }

        $request->validate($rules);

        if ($request->input('estado') === 'activo') {
            $rol = $request->input('rol');
            if ($rol === 'tecnico') {
                $count = User::where('consultora_id', $consultant->id)
                    ->whereIn('rol', ['tecnico', 'technician'])
                    ->where('estado', 'activo')
                    ->when($userId, function ($q) use ($userId) {
                        $q->where('id', '!=', $userId);
                    })
                    ->count();
                if ($count >= $consultant->max_tecnicos) {
                    return back()->withErrors(['rol' => 'Se ha alcanzado el límite de técnicos activos (' . $consultant->max_tecnicos . ') para esta consultora.']);
                }
            } elseif ($rol === 'gestor') {
                $count = User::where('consultora_id', $consultant->id)
                    ->whereIn('rol', ['gestor', 'administrador'])
                    ->where('estado', 'activo')
                    ->when($userId, function ($q) use ($userId) {
                        $q->where('id', '!=', $userId);
                    })
                    ->count();
                if ($count >= $consultant->max_gestores) {
                    return back()->withErrors(['rol' => 'Se ha alcanzado el límite de gestores activos (' . $consultant->max_gestores . ') para esta consultora.']);
                }
            }
        }

        $data = $request->only(['nombres', 'apellidos', 'email', 'rol', 'estado']);
        $data['consultora_id'] = $consultant->id;

        if ($request->input('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        if ($userId) {
            $user = User::findOrFail($userId);
            $user->update($data);
            $message = 'Usuario actualizado exitosamente.';
        } else {
            User::create($data);
            $message = 'Usuario creado exitosamente.';
        }

        return redirect()->route('admin.consultants.users.index', $consultant->id)->with('message', $message);
    }

    /**
     * Destroy a user.
     */
    public function consultantUsersDestroy(Consultora $consultant, User $user)
    {
        $this->ensureSuperAdmin();
        
        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        if ($user->rol === User::ROLE_TECNICO) {
            return back()->with('error', 'No puedes dar de baja a un usuario con rol Técnico desde la súper administración.');
        }

        $user->update(['estado' => 'inactivo']);
        $user->delete();

        return redirect()->route('admin.consultants.users.index', $consultant->id)->with('message', 'Usuario dado de baja exitosamente.');
    }

    /**
     * CRUD de Personal Global (Gestión de Staff)
     */
    public function globalStaffIndex(Request $request)
    {
        $this->ensureSuperAdmin();
        $query = User::query()
            ->whereNull('consultora_id')
            ->whereIn('rol', [User::ROLE_SUPER_ADMIN, User::ROLE_VENDEDOR]);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nombres', 'like', "%{$request->search}%")
                    ->orWhere('apellidos', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $staff = $query->orderBy('apellidos')->orderBy('nombres')->paginate(10)->withQueryString();

        return Inertia::render('Admin/Staff/Index', [
            'staff' => $staff,
            'filters' => $request->only(['search']),
        ]);
    }

    public function globalStaffSave(Request $request)
    {
        $this->ensureSuperAdmin();
        $userId = $request->input('id');

        $rules = [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $userId,
            'rol' => 'required|string|in:super_admin,vendedor',
            'estado' => 'required|string|in:activo,inactivo',
        ];

        if (!$userId) {
            $rules['password'] = 'required|string|min:8';
        } else {
            $rules['password'] = 'nullable|string|min:8';
        }

        $request->validate($rules);

        $data = $request->only(['nombres', 'apellidos', 'email', 'rol', 'estado']);
        $data['consultora_id'] = null; // Personal global

        if ($request->input('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        if ($userId) {
            $user = User::findOrFail($userId);
            $user->update($data);
            $message = 'Miembro del staff global actualizado exitosamente.';
        } else {
            User::create($data);
            $message = 'Miembro del staff global creado exitosamente.';
        }

        return redirect()->route('admin.staff.index')->with('message', $message);
    }

    public function globalStaffDestroy(User $user)
    {
        $this->ensureSuperAdmin();

        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puedes dar de baja tu propio usuario.');
        }

        $user->update(['estado' => 'inactivo']);
        $user->delete();

        return redirect()->route('admin.staff.index')->with('message', 'Miembro del staff global dado de baja exitosamente.');
    }
}
