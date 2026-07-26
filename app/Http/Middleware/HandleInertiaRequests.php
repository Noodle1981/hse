<?php

namespace App\Http\Middleware;

use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     * Disponibles en todas las páginas React via usePage().props
     */
    public function share(Request $request): array
    {
        /** @var User|null $user */
        $user = $request->user();

        // Branding dinámico (igual que el layout Blade actual)
        $consultora = $user?->consultora;
        $sede = $user?->sedes()->first();
        $empresa = $sede?->empresa;

        $pendingResetRequests = 0;
        if ($user) {
            $pendingResetRequests = PasswordResetRequest::query()
                ->where('status', 'pending')
                ->where(function ($q) use ($user) {
                    $q->whereJsonContains('notified_user_ids', $user->id);
                    if ($user->isSuperAdmin()) {
                        $q->orWhereNull('notified_user_ids');
                    }
                })
                ->count();
        }

        return [
            ...parent::share($request),
            'pendingResetRequests' => $pendingResetRequests,
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'nombres' => $user->nombres,
                    'apellidos' => $user->apellidos,
                    'email' => $user->email,
                    'rol' => $user->rol,
                    'is_primary' => (bool) $user->is_primary,
                    'is_primary_gestor' => $user->isPrimaryGestor(),
                    'active_role' => session('active_role', $user->rol),
                    'is_admin' => $user->isAdmin(),
                    'is_technician' => $user->isTechnician(),
                    'is_super_admin' => $user->isSuperAdmin(),
                    'is_vendedor' => $user->isVendedor(),
                    'is_global_user' => $user->isGlobalUser(),
                    'signature' => $user->signature ? (str_starts_with($user->signature, 'data:image') ? $user->signature : asset('storage/'.$user->signature)) : null,
                ] : null,
            ],
            'branding' => [
                'logo' => $consultora?->logo_path
                                    ? asset('storage/'.$consultora->logo_path)
                                    : asset('images/logo.png'),
                'name' => $consultora?->nombre ?? 'EP - EQUIPO PREVENTIVO',
                'primary_color' => $empresa?->primary_color ?? '#F69239',
            ],
            'flash' => [
                'message' => $request->session()->get('message'),
                'error' => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
            ],
        ];
    }
}
