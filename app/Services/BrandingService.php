<?php

namespace App\Services;

use App\Models\User;
use App\Models\Consultora;

class BrandingService
{
    /**
     * Defaults corporativos cuando no hay consultora o sin branding configurado.
     */
    public static function defaults(): array
    {
        return [
            'logo'      => public_path('images/logo.png'),
            'primary'   => '#1e40af',
            'secondary' => '#6b7280',
            'accent'    => '#16a34a',
            'name'      => 'XAMASAFE',
        ];
    }

    /**
     * Resuelve el branding de la consultora del usuario autenticado.
     */
    public static function forUser(?User $user = null): array
    {
        $user = $user ?? auth()->user();
        $consultora = $user?->consultora;

        return self::fromConsultora($consultora);
    }

    /**
     * Resuelve el branding directamente de una Consultora.
     */
    public static function fromConsultora(?Consultora $consultora): array
    {
        if (!$consultora) {
            return self::defaults();
        }

        return [
            'logo'      => $consultora->logo_path
                            ? public_path('storage/' . $consultora->logo_path)
                            : public_path('images/logo.png'),
            'primary'   => $consultora->color_primary   ?? '#1e40af',
            'secondary' => $consultora->color_secondary ?? '#6b7280',
            'accent'    => $consultora->color_accent    ?? '#16a34a',
            'name'      => $consultora->nombre          ?? 'XAMASAFE',
        ];
    }
}
