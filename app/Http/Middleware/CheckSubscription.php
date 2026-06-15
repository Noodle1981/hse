<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && !$user->isSuperAdmin() && !$request->routeIs('logout')) {
            $consultant = $user->consultora;
            if ($consultant) {
                if ($consultant->estado_suscripcion === 'morosa') {
                    abort(403, 'El servicio de su consultora ha sido suspendido temporalmente por falta de pago. Por favor, comuníquese con administración.');
                }
                if ($consultant->estado_suscripcion === 'baja') {
                    abort(403, 'El servicio de su consultora ha sido dado de baja. Por favor, comuníquese con soporte.');
                }
            }
        }

        return $next($request);
    }
}
