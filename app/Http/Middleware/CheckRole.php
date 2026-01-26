<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Verifica que el usuario tenga uno de los roles permitidos
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect('/login');
        }

        $userRole = strtolower($request->user()->rol);
        $allowedRoles = array_map('strtolower', $roles);

        \Log::info('CheckRole Debug:', [
            'user' => $request->user()->email,
            'user_role' => $userRole,
            'allowed_roles' => $allowedRoles
        ]);

        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }

        return $next($request);
    }
}
