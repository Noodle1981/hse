<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DynamicInspection;

class InspectionPolicy
{
    /**
     * Determine if the user can view the inspection.
     */
    public function view(User $user, DynamicInspection $inspection): bool
    {
        // Admin puede ver todas las inspecciones
        if ($user->isAdmin()) {
            return true;
        }

        // Técnico solo puede ver sus propias inspecciones
        return $inspection->user_id === $user->id;
    }

    /**
     * Determine if the user can create inspections.
     */
    public function create(User $user): bool
    {
        // Admin y Técnico pueden crear inspecciones
        return $user->isAdmin() || $user->isTechnician();
    }

    /**
     * Determine if the user can update the inspection.
     */
    public function update(User $user, DynamicInspection $inspection): bool
    {
        // Solo el técnico que creó la inspección puede editarla (si está en borrador)
        return $inspection->user_id === $user->id && $inspection->status === 'draft';
    }

    /**
     * Determine if the user can delete the inspection.
     */
    public function delete(User $user, DynamicInspection $inspection): bool
    {
        // Solo Admin puede eliminar inspecciones
        return $user->isAdmin();
    }
}
