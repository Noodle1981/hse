<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user()) {
            return redirect('login');
        }

        $user = $request->user();

        // Allow super admin to bypass specific role checks
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (! $user->hasRole($roles)) {
            abort(403, 'Acceso Denegado.');
        }

        return $next($request);
    }
}
