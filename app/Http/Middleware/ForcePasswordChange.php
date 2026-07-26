<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password) {
            $currentRoute = $request->route()?->getName();
            $allowedRoutes = ['password.change', 'password.change.update', 'logout'];

            if (! in_array($currentRoute, $allowedRoutes, true)) {
                return redirect()->route('password.change');
            }
        }

        return $next($request);
    }
}
