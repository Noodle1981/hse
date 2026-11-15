<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultora;
use App\Models\User;
use App\Services\ConsultantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ConsultantController extends Controller
{
    public function __construct(private readonly ConsultantService $consultantService) {}

    public function index()
    {
        $this->ensureSuperAdminOrVendedor();

        return Inertia::render('Admin/Consultants/Index', [
            'consultants' => $this->consultantService->getPaginatedConsultants(),
        ]);
    }

    public function create()
    {
        $this->ensureSuperAdminOrVendedor();

        return Inertia::render('Admin/Consultants/Edit', ['consultant' => null]);
    }

    public function store(Request $request)
    {
        $this->ensureSuperAdminOrVendedor();
        $this->consultantService->createConsultant(Auth::user(), $request, $request->validate($this->consultantRules()));

        return redirect()->route('admin.consultants.index')->with('message', 'Consultora creada exitosamente.');
    }

    public function edit(Consultora $consultant)
    {
        $this->ensureSuperAdminOrVendedor();

        return Inertia::render('Admin/Consultants/Edit', $this->consultantService->getEditData($consultant));
    }

    public function update(Request $request, Consultora $consultant)
    {
        $this->ensureSuperAdminOrVendedor();
        $this->consultantService->updateConsultant($consultant, $request, $request->validate($this->consultantRules($consultant->id)));

        return redirect()->route('admin.consultants.index')->with('message', 'Consultora actualizada exitosamente.');
    }

    public function destroy(Consultora $consultant)
    {
        if (! Auth::user()->isSuperAdmin()) {
            abort(403, 'Acceso restringido a Super Administradores.');
        }

        if ($consultant->users()->count() > 0) {
            return back()->with('error', 'No se puede eliminar una consultora con usuarios activos.');
        }

        $consultant->delete();

        return redirect()->route('admin.consultants.index')->with('message', 'Consultora eliminada exitosamente.');
    }

    public function usersIndex(Consultora $consultant)
    {
        $this->ensureSuperAdminOrVendedor();

        return Inertia::render('Admin/Consultants/Users', [
            'consultant' => $consultant,
            'users' => $consultant->users()->get(),
        ]);
    }

    public function usersSave(Request $request, Consultora $consultant)
    {
        $this->ensureSuperAdminOrVendedor();
        $userId = $request->input('id') ? (int) $request->input('id') : null;

        $validated = $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$userId,
            'rol' => 'required|string|in:gestor,tecnico',
            'estado' => 'required|string|in:activo,inactivo',
        ]);

        $result = $this->consultantService->saveConsultantUser($consultant, $validated, $userId);
        if (isset($result['error'])) {
            return back()->withErrors([$result['error_field'] => $result['error']]);
        }

        return redirect()->route('admin.consultants.users.index', $consultant->id)
            ->with('message', $result['message']);
    }

    public function usersResetPassword(Consultora $consultant, User $user)
    {
        $this->ensureSuperAdminOrVendedor();
        abort_unless($user->consultora_id === $consultant->id, 403, 'Acceso no autorizado a este usuario.');

        $this->consultantService->resetTemporaryPassword($user);

        return back()->with('message', "Contraseña de {$user->full_name} reseteada exitosamente.");
    }

    public function usersDestroy(Consultora $consultant, User $user)
    {
        $this->ensureSuperAdminOrVendedor();
        abort_unless($user->consultora_id === $consultant->id, 403, 'Acceso no autorizado a este usuario.');

        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puedes deshabilitar tu propio usuario.');
        }

        $user->update(['estado' => 'inactivo']);

        return redirect()->route('admin.consultants.users.index', $consultant->id)
            ->with('message', 'Usuario deshabilitado exitosamente.');
    }

    public function savePrimaryGestor(Request $request, Consultora $consultant)
    {
        $this->ensureSuperAdminOrVendedor();

        if ($request->has('email')) {
            $request->merge(['email' => strtolower(trim($request->email))]);
        }

        $validated = $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($consultant->primaryGestor?->id)],
            'telefono' => 'nullable|string|max:50',
            'n_doc' => 'nullable|string|max:50',
            'matricula' => 'nullable|string|max:50',
        ]);

        $message = $this->consultantService->savePrimaryGestor($consultant, $validated);

        return back()->with('message', $message);
    }

    public function resetPrimaryPassword(Consultora $consultant)
    {
        $this->ensureSuperAdminOrVendedor();

        $primaryGestor = $consultant->primaryGestor;
        if (! $primaryGestor) {
            return back()->with('error', 'La consultora no tiene un Gestor Principal asignado.');
        }

        $this->consultantService->resetTemporaryPassword($primaryGestor);

        return back()->with('message', 'Contraseña del Gestor Principal reseteada exitosamente. Comuníquele la nueva clave temporal.');
    }

    private function ensureSuperAdminOrVendedor(): void
    {
        $user = Auth::user();
        if (! ($user->isSuperAdmin() || $user->isVendedor())) {
            abort(403, 'Acceso restringido a Súper Administradores o Vendedores.');
        }
    }

    private function consultantRules(?int $ignoreId = null): array
    {
        $cuitUnique = $ignoreId ? 'unique:consultoras,cuit,'.$ignoreId : 'unique:consultoras,cuit';

        return [
            'nombre' => 'required|string|max:255',
            'cuit' => 'required|string|max:20|'.$cuitUnique,
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
        ];
    }
}
