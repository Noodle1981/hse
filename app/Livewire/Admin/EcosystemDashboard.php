<?php

namespace App\Livewire\Admin;

use App\Models\Empresa;
use Livewire\Component;

class EcosystemDashboard extends Component
{
    public function render()
    {
        $companies = Empresa::withCount([
            'sedes',
            'assignments',
            // 'dynamicInspections', // Necesito verificar si tiene esta relación
        ])->get();

        // Totales globales
        $stats = [
            'total_companies' => $companies->count(),
            'total_sedes' => $companies->sum('sedes_count'),
            'total_inspections' => \App\Models\DynamicInspection::count(),
            'total_active_users' => \App\Models\User::where('estado', 'activo')->count(),
        ];

        return view('livewire.admin.ecosystem-dashboard', [
            'companies' => $companies,
            'stats' => $stats,
        ])->layout('components.layouts.admin');
    }
}
