<?php

namespace App\Services;

use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class PasswordResetRequestService
{
    public function createRequest(string $email): array
    {
        $user = User::where('email', $email)->where('estado', 'activo')->first();

        if (! $user) {
            return [
                'type' => 'message',
                'text' => 'Si el correo ingresado se encuentra registrado y activo, tu administrador recibirá la solicitud.',
            ];
        }

        $existingRequest = PasswordResetRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return [
                'type' => 'warning',
                'text' => 'Ya tienes una solicitud de reseteo pendiente. Tu administrador ya ha sido notificado.',
            ];
        }

        $notifiedUserIds = [];

        if ($user->isTechnician() || ($user->isGestor() && ! $user->is_primary)) {
            $primaryGestor = User::where('consultora_id', $user->consultora_id)
                ->where('is_primary', true)
                ->where('estado', 'activo')
                ->first();

            if ($primaryGestor) {
                $notifiedUserIds[] = $primaryGestor->id;
            } else {
                $notifiedUserIds = User::where('rol', User::ROLE_SUPER_ADMIN)->where('estado', 'activo')->pluck('id')->toArray();
            }
        } else {
            $superAdmins = User::where('rol', User::ROLE_SUPER_ADMIN)->where('estado', 'activo')->pluck('id')->toArray();
            $creatorId = $user->consultora?->created_by_user_id;

            $notifiedUserIds = array_values(array_unique(array_filter(array_merge($superAdmins, [$creatorId]))));
        }

        PasswordResetRequest::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'notified_user_ids' => $notifiedUserIds,
        ]);

        return [
            'type' => 'message',
            'text' => 'Solicitud enviada exitosamente. Tu administrador ha sido notificado en la plataforma.',
        ];
    }

    public function getPaginatedRequests(User $currentUser): LengthAwarePaginator
    {
        $query = PasswordResetRequest::with(['user.consultora', 'resolvedBy'])->latest();

        if (! $currentUser->isSuperAdmin()) {
            $query->where('status', 'pending')
                ->whereJsonContains('notified_user_ids', $currentUser->id);
        }

        return $query->paginate(15)->through(function ($r) {
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
    }
}
