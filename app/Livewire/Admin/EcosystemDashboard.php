<?php

namespace App\Livewire\Admin;

use App\Models\Empresa;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
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
            'companies'     => $companies,
            'stats'         => $stats,
            'recent_events' => $this->getRecentEvents(),
        ]);
    }

    /**
     * Consolidar últimos eventos para la terminal de auditoría
     */
    private function getRecentEvents()
    {
        $limit = 20;

        $visits = \App\Models\VisitCertificate::with(['sede', 'empresa'])->latest()->take($limit)->get()
            ->map(fn($v) => [
                'type' => 'visit',
                'label' => 'Certificación',
                'description' => "Visita certificada en {$v->sede->nombre_sede} ({$v->empresa->nombre})",
                'time' => $v->created_at,
                'color' => 'green'
            ]);

        $incidents = \App\Models\Incidente::with(['sede.empresa'])->latest()->take($limit)->get()
            ->map(fn($i) => [
                'type' => 'incident',
                'label' => 'Incidente',
                'description' => "ALERTA: {$i->tipo} {$i->gravedad} en {$i->sede->nombre_sede}",
                'time' => $i->created_at,
                'color' => 'red'
            ]);

        $findings = \App\Models\Hallazgo::with(['sede.empresa'])->latest()->take($limit)->get()
            ->map(fn($h) => [
                'type' => 'finding',
                'label' => 'Hallazgo',
                'description' => "Detección: {$h->identificador} en {$h->sede->nombre_sede}",
                'time' => $h->created_at,
                'color' => 'ep-primary'
            ]);

        $reports = \App\Models\Informe::with(['sede.empresa'])->latest()->take($limit)->get()
            ->map(fn($r) => [
                'type' => 'report',
                'label' => 'Informe',
                'description' => "Generación: {$r->titulo} para {$r->sede->nombre_sede}",
                'time' => $r->created_at,
                'color' => 'blue'
            ]);

        return $visits->concat($incidents)->concat($findings)->concat($reports)
            ->sortByDesc('time')
            ->take($limit);
    }
}
