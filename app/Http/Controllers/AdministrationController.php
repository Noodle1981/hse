<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\User;
use App\Services\DashboardMetricsService;
use App\Services\UserManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AdministrationController extends Controller
{
    public function __construct(
        private readonly DashboardMetricsService $dashboardMetricsService,
        private readonly UserManagementService $userManagementService
    ) {}

    public function hub()
    {
        return Inertia::render('Gestor/Administration/Hub');
    }

    public function users(Request $request)
    {
        return Inertia::render(
            'Gestor/Administration/Users/Index',
            $this->userManagementService->getUsersData(Auth::user(), $request)
        );
    }

    public function technicians()
    {
        return Inertia::render(
            'Gestor/Administration/Technicians/Index',
            $this->userManagementService->getTechniciansData(Auth::user())
        );
    }

    public function getCompanySites(Empresa $empresa)
    {
        $currentUser = Auth::user();
        if (! $currentUser->isSuperAdmin() && $empresa->consultora_id !== $currentUser->consultora_id) {
            abort(403, 'Acceso no autorizado a esta empresa.');
        }

        return response()->json($empresa->sedes()->get(['id', 'nombre_sede', 'empresa_id']));
    }

    public function getTechnicianAssignments(User $user)
    {
        $this->userManagementService->ensureTenancy(Auth::user(), $user);

        return response()->json($user->sedes()->with('empresa')->get());
    }

    public function assignSede(Request $request, User $user)
    {
        $this->userManagementService->ensureTenancy(Auth::user(), $user);

        $request->validate(['sede_id' => 'required|exists:sedes,id']);

        if ($user->sedes()->where('sedes.id', $request->sede_id)->exists()) {
            return back()->with('error', 'El técnico ya tiene asignada esta sede.');
        }

        $user->sedes()->attach($request->sede_id);

        return back()->with('message', 'Sede asignada correctamente.');
    }

    public function removeAssignment(Request $request, User $user)
    {
        $this->userManagementService->ensureTenancy(Auth::user(), $user);

        $request->validate(['sede_id' => 'required|exists:sedes,id']);
        $user->sedes()->detach($request->sede_id);

        return back()->with('message', 'Asignación eliminada correctamente.');
    }

    public function gestorUsersResetPassword(User $user)
    {
        $message = $this->userManagementService->resetGestorUserPassword(Auth::user(), $user);

        return back()->with('message', $message);
    }

    public function saveUser(Request $request)
    {
        $editingId = $request->input('id');

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

        $validated = $request->validate($rules, [
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
        ]);

        $result = $this->userManagementService->saveUser(Auth::user(), $request, $validated);
        if (isset($result['error'])) {
            return back()->withErrors([$result['error_field'] => $result['error']]);
        }

        return back()->with('message', $result['message']);
    }

    public function deleteUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $this->userManagementService->deleteUser(Auth::user(), $user);

        return back()->with('message', 'Usuario dado de baja exitosamente.');
    }

    public function restoreUser($id)
    {
        $result = $this->userManagementService->restoreUser(Auth::user(), $id);
        if (isset($result['error'])) {
            return back()->with('error', $result['error']);
        }

        return back()->with('message', $result['message']);
    }

    public function dashboard(Request $request)
    {
        $user = Auth::user();
        if (! ($user->isSuperAdmin() || $user->isVendedor())) {
            abort(403, 'Acceso restringido a Súper Administradores o Vendedores.');
        }

        return Inertia::render(
            'Admin/Dashboard',
            $this->dashboardMetricsService->getAdminDashboardData($request)
        );
    }

    public function gestorDashboard(Request $request)
    {
        return Inertia::render(
            'Gestor/Dashboard',
            $this->dashboardMetricsService->getGestorDashboardData(Auth::user(), $request)
        );
    }
}
