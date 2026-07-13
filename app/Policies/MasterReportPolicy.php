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

        // El Técnico ve sus propios informes o informes de sus sedes asignadas
        if ($user->isTechnician()) {
            return $user->id === $report->user_id || $user->sedes()->where('sedes.id', $report->sede_id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can update the master report.
     */
    public function update(User $user, MasterReport $report): bool
    {
        // El Super Admin tiene acceso total
        if ($user->isSuperAdmin()) {
            return true;
        }

        // El Gestor puede actualizar si el reporte pertenece a su consultora (para aprobar u observar)
        if ($user->isGestor()) {
            return $user->consultora_id === ($report->sede?->empresa?->consultora_id ?? null);
        }

        // El Técnico solo edita sus propios informes cuando están en borrador/redacción
        if ($user->isTechnician()) {
            return $user->id === $report->user_id && in_array($report->estado, ['en_redaccion', 'solicitado']);
        }

        return false;
    }
}
