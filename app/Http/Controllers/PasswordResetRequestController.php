<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PasswordResetRequestController extends Controller
{
    /**
     * Crear una solicitud de reseteo (desde la pantalla de Login sin autenticación)
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->where('estado', 'activo')->first();

        // Si el usuario no existe, devolvemos mensaje neutro por seguridad
        if (! $user) {
            return back()->with('message', 'Si el correo ingresado se encuentra registrado y activo, tu administrador recibirá la solicitud.');
        }

        // Verificar si ya tiene una solicitud pendiente
        $existingRequest = PasswordResetRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return back()->with('warning', 'Ya tienes una solicitud de reseteo pendiente. Tu administrador ya ha sido notificado.');
        }

        // Determinar destinatarios según la jerarquía
        $notifiedUserIds = [];

        if ($user->isTechnician() || ($user->isGestor() && ! $user->is_primary)) {
            // Técnico o Gestor secundario -> Notifica al Gestor Principal de su consultora
            $primaryGestor = User::where('consultora_id', $user->consultora_id)
                ->where('is_primary', true)
                ->where('estado', 'activo')
                ->first();

            if ($primaryGestor) {
                $notifiedUserIds[] = $primaryGestor->id;
            } else {
                // Fallback: si no hay gestor principal, notifica a los super admins
                $notifiedUserIds = User::where('rol', User::ROLE_SUPER_ADMIN)->where('estado', 'activo')->pluck('id')->toArray();
            }
        } else {
            // Gestor Principal, Super Admin o Vendedor -> Notifica a los Super Admins + Vendedor Creador de la consultora
            $superAdmins = User::where('rol', User::ROLE_SUPER_ADMIN)->where('estado', 'activo')->pluck('id')->toArray();
            $creatorId = $user->consultora?->created_by_user_id;

            $notifiedUserIds = array_values(array_unique(array_filter(array_merge($superAdmins, [$creatorId]))));
        }

        PasswordResetRequest::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'notified_user_ids' => $notifiedUserIds,
        ]);

        return back()->with('message', 'Solicitud enviada exitosamente. Tu administrador ha sido notificado en la plataforma.');
    }

    /**
     * Listar solicitudes pendientes de reseteo para el usuario autenticado
     */
    public function index()
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $query = PasswordResetRequest::with(['user.consultora', 'resolvedBy'])
            ->latest();

        if (! $currentUser->isSuperAdmin()) {
            $query->where('status', 'pending')
                ->whereJsonContains('notified_user_ids', $currentUser->id);
        }

        $requests = $query->paginate(15)->through(function ($r) {
            return [
                'id' => $r->id,
                'status' => $r->status,
                'created_at' => $r->created_at ? $r->created_at->toISOString() : null,
                'created_at_formatted' => $r->created_at ? $r->created_at->format('d/m/Y H:i') : 'N/A',
                'created_at_human' => $r->created_at ? $r->created_at->diffForHumans() : '',
                'user' => $r->user ? [
                    'id' => $r->user->id,
                    'nombres' => $r->user->nombres,
                    'apellidos' => $r->user->apellidos,
                    'full_name' => $r->user->full_name,
                    'email' => $r->user->email,
                    'rol' => $r->user->rol,
                    'is_primary' => (bool) $r->user->is_primary,
                    'consultora' => $r->user->consultora ? [
                        'id' => $r->user->consultora->id,
                        'nombre' => $r->user->consultora->nombre,
                    ] : null,
                ] : null,
                'resolved_by' => $r->resolvedBy ? [
                    'name' => $r->resolvedBy->full_name,
                    'email' => $r->resolvedBy->email,
                ] : null,
            ];
        });

        return Inertia::render('Admin/PasswordResetRequests/Index', [
            'requests' => $requests,
        ]);
    }

    /**
     * Aprobar / Resolver solicitud generando clave temporal
     */
    public function resolve(PasswordResetRequest $resetRequest)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        // Validar permisos
        if (! $currentUser->isSuperAdmin()) {
            $notifiedIds = $resetRequest->notified_user_ids ?? [];
            if (! in_array($currentUser->id, $notifiedIds, true)) {
                abort(403, 'No tienes permisos para resolver esta solicitud de reseteo.');
            }
        }

        $targetUser = $resetRequest->user;
        if (! $targetUser) {
            return back()->with('error', 'El usuario asociado a esta solicitud ya no existe.');
        }

        $plainPassword = Str::random(16);
        $targetUser->update([
            'password' => Hash::make($plainPassword),
            'must_change_password' => true,
        ]);

        $resetRequest->update([
            'status' => 'resolved',
            'resolved_by_user_id' => $currentUser->id,
        ]);

        session()->flash('generated_password', $plainPassword);

        return back()->with('message', "Solicitud resuelta. Clave temporal generada para {$targetUser->full_name}.");
    }

    /**
     * Descartar / Rechazar solicitud
     */
    public function destroy(PasswordResetRequest $resetRequest)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        if (! $currentUser->isSuperAdmin()) {
            $notifiedIds = $resetRequest->notified_user_ids ?? [];
            if (! in_array($currentUser->id, $notifiedIds, true)) {
                abort(403, 'No tienes permisos para descartar esta solicitud.');
            }
        }

        $resetRequest->update([
            'status' => 'rejected',
            'resolved_by_user_id' => $currentUser->id,
        ]);

        return back()->with('message', 'Solicitud de reseteo descartada.');
    }
}
