<?php

namespace App\Policies;

use App\Models\GeneradorConstancia;
use App\Models\User;

class GeneradorConstanciaPolicy
{
    /**
     * Determine if the user can view the certificate.
     */
    public function view(User $user, GeneradorConstancia $constancia): bool
    {
        // Admin puede ver todas
        if ($user->isGestor()) {
            return true;
        }

        // Técnico solo puede ver las suyas
        return $user->id === $constancia->user_id;
    }

    /**
     * Determine if the user can create certificates.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isTechnician();
    }

    /**
     * Determine if the user can update the certificate.
     */
    public function update(User $user, GeneradorConstancia $constancia): bool
    {
        // Solo el técnico que la creó puede actualizarla
        return $user->id === $constancia->user_id;
    }

    /**
     * Determine if the user can delete the certificate.
     */
    public function delete(User $user, GeneradorConstancia $constancia): bool
    {
        // Solo admin puede eliminar
        return $user->isGestor();
    }
}
