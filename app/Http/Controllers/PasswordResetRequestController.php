<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetRequest;
use App\Models\User;
use App\Services\PasswordResetRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PasswordResetRequestController extends Controller
{
    public function __construct(protected PasswordResetRequestService $resetService) {}

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
        $result = $this->resetService->createRequest($email);

        return back()->with($result['type'], $result['text']);
    }

    /**
     * Listar solicitudes pendientes de reseteo para el usuario autenticado
     */
    public function index()
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        return Inertia::render('Admin/PasswordResetRequests/Index', [
            'requests' => $this->resetService->getPaginatedRequests($currentUser),
        ]);
    }

    /**
     * Aprobar / Resolver solicitud generando clave temporal
     */
    public function resolve(PasswordResetRequest $resetRequest)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

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
