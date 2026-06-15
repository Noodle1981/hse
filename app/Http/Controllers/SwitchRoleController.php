<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SwitchRoleController extends Controller
{
    /**
     * Alterna el rol activo del usuario en la sesión.
     */
    public function switch(Request $request, $role)
    {
        /** @var User $user */
        $user = Auth::user();

        // Validar que el usuario tenga permiso para alternar
        // Solo Gestores pueden alternar a modo Técnico
        if (! $user || $user->rol !== User::ROLE_GESTOR) {
            abort(403, 'No tienes permisos para alternar roles.');
        }

        // Validar el rol solicitado
        $targetRole = strtolower($role);
        if (! in_array($targetRole, [User::ROLE_GESTOR, User::ROLE_TECNICO])) {
            abort(400, 'Rol no válido para alternancia.');
        }

        // Si es Gestor y pide volver a gestor, simplemente limpiamos la sesión
        if ($targetRole === User::ROLE_GESTOR) {
            session()->forget('active_role');
        } else {
            session(['active_role' => $targetRole]);
        }

        // Redirigir según el nuevo rol activo
        if ($targetRole === User::ROLE_TECNICO) {
            return redirect()->route('tecnico.checklists.tasks.index')->with('message', 'Modo Técnico activado.');
        }

        return redirect()->route('gestor.dashboard')->with('message', 'Modo Gestor activado.');
    }
}
