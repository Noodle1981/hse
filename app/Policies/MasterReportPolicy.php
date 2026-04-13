<?php

namespace App\Policies;

use App\Models\MasterReport;
use App\Models\User;

class MasterReportPolicy
{
    /**
     * Determine whether the user can view the master report.
     */
    public function view(User $user, MasterReport $report): bool
    {
        // El Super Admin tiene acceso total
        if ($user->isSuperAdmin()) {
            return true;
        }

        // El Gestor ve reportes de su propia consultora
        if ($user->isGestor()) {
            // Relación: MasterReport -> Sede -> Empresa -> Consultora
            return $user->consultora_id === ($report->sede?->empresa?->consultora_id ?? null);
        }

        // El Técnico solo ve sus propios informes
        if ($user->isTechnician()) {
            return $user->id === $report->user_id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the master report.
     */
    public function update(User $user, MasterReport $report): bool
    {
        // Solo el autor (tecnico) o el super admin pueden editar reportes en borrador
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isTechnician()) {
            return $user->id === $report->user_id && $report->estado !== 'finalizado';
        }

        return false;
    }
}
