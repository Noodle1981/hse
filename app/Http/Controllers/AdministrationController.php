<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\AnnualPlan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\DynamicInspection;
use App\Models\ChecklistAssignment;
use App\Models\Training;
use App\Models\Hallazgo;
use App\Models\Incidente;
use App\Models\MasterReport;
use Illuminate\Support\Facades\DB;
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

        if (!$currentUser->isSuperAdmin()) {
            $query->where('consultora_id', $currentUser->consultora_id);
        }

        if ($request->boolean('showDeleted')) {
            $query->onlyTrashed();
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
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

        if (!$currentUser->isSuperAdmin()) {
            $query->where('consultora_id', $currentUser->consultora_id);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
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

        $rules = [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($editingId)],
            'rol' => 'required|in:gestor,tecnico',
            'matricula' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($editingId)],
            'telefono' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($editingId)],
            'estado' => 'required|in:activo,inactivo',
        ];

        if (!$editingId || $request->password) {
            $rules['password'] = 'required|string|min:6';
        }

        $request->validate($rules);

        $data = $request->only(['nombres', 'apellidos', 'email', 'rol', 'matricula', 'telefono', 'estado']);

        if (!$currentUser->isSuperAdmin()) {
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

    /**
     * Restore soft deleted user.
     */
    public function restoreUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $this->ensureTenancy($user);
        
        $user->restore();
        $user->update(['estado' => 'activo']);

        return back()->with('message', 'Usuario restaurado exitosamente.');
    }

    /**
     * Show the super admin dashboard.
     */
    public function dashboard(Request $request)
    {
        $this->ensureSuperAdmin();

        $from = $request->query('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->query('to', now()->endOfMonth()->format('Y-m-d'));

        // KPIs
        $kpis = [
            'inspections' => DynamicInspection::whereBetween('completed_at', [$from, $to])->completed()->count(),
            'findings' => Hallazgo::whereBetween('fecha_deteccion', [$from, $to])->count(),
            'incidents' => Incidente::whereBetween('fecha_ocurrencia', [$from, $to])->count(),
            'trainings' => Training::whereBetween('date', [$from, $to])->completed()->count(),
            'critical_findings' => Hallazgo::where('criticidad', 'critico')->where('estado', '!=', 'cerrado')->count(),
            'pending_assignments' => ChecklistAssignment::where('status', 'pending')->count(),
        ];

        // Recent Activity (Merged feed)
        $recentActivity = $this->getRecentActivity();

        // Rankings
        $rankings = [
            'top_technicians' => User::where('rol', 'tecnico')
                ->withCount(['checklists' => fn($q) => $q->where('status', 'completed')])
                ->orderByDesc('checklists_count')
                ->take(5)
                ->get(),
            'top_companies_incidents' => Empresa::withCount('incidentes')
                ->orderByDesc('incidentes_count')
                ->take(5)
                ->get(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'kpis' => $kpis,
            'recentActivity' => $recentActivity,
            'rankings' => $rankings,
            'filters' => [
                'from' => $from,
                'to' => $to,
            ]
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

        $from = $request->query('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->query('to', now()->endOfMonth()->format('Y-m-d'));

        // global KPIs (Filtered by consultora)
        $kpis = [
            'total_companies' => Empresa::where('consultora_id', $consultoraId)->count(),
            'total_inspections' => DynamicInspection::whereBetween('completed_at', [$from, $to])
                ->whereHas('user', fn($q) => $q->where('consultora_id', $consultoraId))
                ->completed()
                ->count(),
            'total_findings' => Hallazgo::whereBetween('fecha_deteccion', [$from, $to])
                ->whereHas('sede.empresa', fn($q) => $q->where('consultora_id', $consultoraId))
                ->count(),
            'total_incidents' => Incidente::whereBetween('fecha_ocurrencia', [$from, $to])
                ->whereHas('sede.empresa', fn($q) => $q->where('consultora_id', $consultoraId))
                ->count(),
            'total_reports' => MasterReport::whereBetween('updated_at', [$from, $to])
                ->whereHas('sede.empresa', fn($q) => $q->where('consultora_id', $consultoraId))
                ->where('estado', 'finalizado')
                ->count(),
        ];

        // Intelligence Matrix: Companies with metrics
        $companies = Empresa::where('consultora_id', $consultoraId)
            ->withCount([
                'inspecciones' => fn($q) => $q->whereBetween('completed_at', [$from, $to])->completed(),
                'hallazgos' => fn($q) => $q->whereBetween('fecha_deteccion', [$from, $to]),
                'incidentes' => fn($q) => $q->whereBetween('fecha_ocurrencia', [$from, $to]),
                'informes' => fn($q) => $q->where('estado', 'finalizado'),
                'checklistAssignments as active_assignments_count',
                'sedes as sedes_con_plan_count' => fn($q) => $q->whereHas('annualPlans', fn($p) => $p->where('year', now()->year))
            ])
            ->orderBy('nombre')
            ->get();

        // Efficiency (Plan vs Done)
        $efficiency = $this->calculateConsultoraEfficiency($consultoraId);

        // Tactical Terminal (Unified Activity Log)
        $recentEvents = $this->getTacticalTerminalEvents($consultoraId);

        return Inertia::render('Gestor/Dashboard', [
            'kpis' => $kpis,
            'companies' => $companies,
            'efficiency' => $efficiency,
            'recentEvents' => $recentEvents,
            'filters' => [
                'from' => $from,
                'to' => $to,
            ]
        ]);
    }

    private function getTacticalTerminalEvents($consultoraId)
    {
        $limit = 15;

        // 1. Inspections
        $inspections = DynamicInspection::with(['checklist', 'user', 'sede.empresa'])
            ->whereHas('user', fn($q) => $q->where('consultora_id', $consultoraId))
            ->completed()
            ->latest('completed_at')
            ->limit($limit)
            ->get()
            ->map(fn($i) => [
                'time' => $i->completed_at,
                'label' => 'INSPECCIÓN',
                'color' => 'emerald',
                'description' => "{$i->user->full_name} finalizó '{$i->checklist->title}' en {$i->sede->empresa->nombre}",
                'diffText' => $i->completed_at->diffForHumans()
            ]);

        // 2. Hallazgos
        $findings = Hallazgo::with(['sede.empresa'])
            ->whereHas('sede.empresa', fn($q) => $q->where('consultora_id', $consultoraId))
            ->latest('fecha_deteccion')
            ->limit($limit)
            ->get()
            ->map(fn($h) => [
                'time' => $h->created_at,
                'label' => 'HALLAZGO',
                'color' => 'yellow',
                'description' => "Nuevo hallazgo '{$h->titulo}' detectado en {$h->sede->empresa->nombre} (Nivel: {$h->criticidad})",
                'diffText' => $h->created_at->diffForHumans()
            ]);

        // 3. Incidentes
        $incidents = Incidente::with(['sede.empresa'])
            ->whereHas('sede.empresa', fn($q) => $q->where('consultora_id', $consultoraId))
            ->latest('fecha_ocurrencia')
            ->limit($limit)
            ->get()
            ->map(fn($inc) => [
                'time' => $inc->created_at,
                'label' => 'SINIESTRO',
                'color' => 'red',
                'description' => "Siniestro reportado en {$inc->sede->empresa->nombre}: {$inc->tipo}",
                'diffText' => $inc->created_at->diffForHumans()
            ]);

        // 4. Reportes
        $reports = MasterReport::with(['sede.empresa'])
            ->whereHas('sede.empresa', fn($q) => $q->where('consultora_id', $consultoraId))
            ->where('estado', 'finalizado')
            ->latest('updated_at')
            ->limit($limit)
            ->get()
            ->map(fn($r) => [
                'time' => $r->updated_at,
                'label' => 'REPORTE',
                'color' => 'blue',
                'description' => "Informe Maestro consolidado para {$r->sede->empresa->nombre} ({$r->periodo})",
                'diffText' => $r->updated_at->diffForHumans()
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

    private function calculateConsultoraEfficiency($consultoraId)
    {
        $year = now()->year;
        $month = now()->month;
        
        $totalPlanned = AnnualPlan::whereHas('sede.empresa', fn($q) => $q->where('consultora_id', $consultoraId))
            ->where('scheduled_months', 'like', "%{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "%")
            ->count();

        $totalDone = DynamicInspection::whereHas('user', fn($q) => $q->where('consultora_id', $consultoraId))
            ->whereYear('completed_at', $year)
            ->whereMonth('completed_at', $month)
            ->completed()
            ->count();

        return [
            'percentage' => $totalPlanned > 0 ? round(($totalDone / $totalPlanned) * 100) : 0,
            'planned' => $totalPlanned,
            'done' => $totalDone
        ];
    }

    private function getConsultoraActivity($consultoraId)
    {
        return DynamicInspection::with(['checklist', 'user', 'sede.empresa'])
            ->whereHas('user', fn($q) => $q->where('consultora_id', $consultoraId))
            ->completed()
            ->latest('completed_at')
            ->limit(10)
            ->get()
            ->map(fn($i) => [
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
            ->map(fn($i) => [
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
            ->map(fn($i) => [
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
        if (!$user->isSuperAdmin()) {
            abort(403, 'Acceso restringido a Super Administradores.');
        }
    }

    private function ensureTenancy(User $user)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        if (!$currentUser->isSuperAdmin() && $user->consultora_id !== $currentUser->consultora_id) {
            abort(403, 'Acceso no autorizado.');
        }
    }
}
