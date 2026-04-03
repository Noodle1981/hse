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
            'checklistAssignments',
            'hallazgos',
            'incidentes',
            'informes',
            'visitTrainings',
            // Sedes que TIENEN Plan Anual (Contratos Activos)
            'sedes as sedes_con_plan_count' => function ($query) {
                $query->whereHas('annualPlans');
            },
            // Checklists realizados de forma LIBRE (Autogestión)
            'visitChecklistCompletions as checklists_libres_count' => function ($query) {
                $query->where('is_self_assigned', true);
            },
            // Capacitaciones realizadas de forma LIBRE (Ad-hoc)
            'visitTrainings as trainings_libres_count' => function ($query) {
                $query->where('is_adhoc', true);
            }
        ])->get();

        // Totales globales recalculados
        $stats = [
            'total_companies'   => $companies->count(),
            'total_sedes'       => $companies->sum('sedes_count'),
            'total_visits'      => \App\Models\VisitCertificate::count(),
            'total_active_users'=> \App\Models\User::where('estado', 'activo')->count(),
            'total_findings'    => \App\Models\Hallazgo::count(),
            'total_incidents'   => \App\Models\Incidente::count(),
            'total_reports'     => \App\Models\Informe::count(),
        ];

        return view('livewire.admin.ecosystem-dashboard', [
            'companies' => $companies,
            'stats'     => $stats,
        ])->layout('components.layouts.admin');
    }
}
