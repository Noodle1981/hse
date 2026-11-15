<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class GlobalStaffController extends Controller
{
    public function __construct(private readonly UserManagementService $userManagementService) {}

    public function index(Request $request)
    {
        $this->ensureSuperAdmin();

        return Inertia::render(
            'Admin/Staff/Index',
            $this->userManagementService->getGlobalStaffData($request)
        );
    }

    public function save(Request $request)
    {
        $this->ensureSuperAdmin();
        $userId = $request->input('id') ? (int) $request->input('id') : null;

        $validated = $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$userId,
            'rol' => 'required|string|in:super_admin,vendedor',
            'estado' => 'required|string|in:activo,inactivo',
            'password' => $userId ? 'nullable|string|min:8' : 'required|string|min:8',
        ]);

        $message = $this->userManagementService->saveGlobalStaff($validated, $userId, $request->input('password'));

        return redirect()->route('admin.staff.index')->with('message', $message);
    }

    public function destroy(User $user)
    {
        $this->ensureSuperAdmin();

        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puedes deshabilitar tu propio usuario.');
        }

        $user->update(['estado' => 'inactivo']);

        return redirect()->route('admin.staff.index')->with('message', 'Miembro del staff global deshabilitado exitosamente.');
    }

    private function ensureSuperAdmin(): void
    {
        if (! Auth::user()->isSuperAdmin()) {
            abort(403, 'Acceso restringido a Super Administradores.');
        }
    }
}
