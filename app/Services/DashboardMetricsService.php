<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\Consultora;
use App\Models\DynamicChecklist;
use App\Models\DynamicInspection;
use App\Models\Empresa;
use App\Models\Hallazgo;
use App\Models\Incidente;
use App\Models\MasterReport;
use App\Models\Training;
use App\Models\User;
use App\Models\VisitCertificate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardMetricsService
{
    /**
     * Build SuperAdmin / Vendedor dashboard metrics, optionally scoped to one consultora.
     */
    public function getAdminDashboardData(Request $request): array
    {
        $from = $request->query('from', now()->subMonths(1)->startOfMonth()->format('Y-m-d'));
        $to = $request->query('to', now()->endOfMonth()->format('Y-m-d'));
        $consultoraId = $request->query('consultora_id', 'all');

        $consultants = Consultora::orderBy('nombre')->get(['id', 'nombre']);
        $range = [$from.' 00:00:00', $to.' 23:59:59'];

        if ($consultoraId && $consultoraId !== 'all') {
            $consultora = Consultora::findOrFail($consultoraId);
            $consultoraName = $consultora->nombre;

            $empresasCount = $consultora->empresas()->count();
            $gestoresCount = User::where('consultora_id', $consultora->id)->where('rol', 'gestor')->count();
            $tecnicosCount = User::where('consultora_id', $consultora->id)->where('rol', 'tecnico')->count();

            $inspectionsCount = DynamicInspection::whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $consultora->id))
                ->whereBetween('dynamic_inspections.completed_at', $range)
                ->where('dynamic_inspections.status', 'completed')
                ->count();

            $trainingsCount = Training::whereHas('empresa', fn ($q) => $q->where('consultora_id', $consultora->id))
                ->whereBetween('trainings.date', $range)
                ->where('trainings.status', 'completed')
                ->count();

            $masterReportsCount = MasterReport::whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $consultora->id))
                ->whereBetween('master_reports.created_at', $range)
                ->whereIn('master_reports.estado', ['finalizado', 'aprobado'])
                ->count();

            $companiesQuery = Empresa::where('consultora_id', $consultora->id);
        } else {
            $consultoraName = 'Todas las Consultoras';

            $empresasCount = Empresa::count();
            $gestoresCount = User::where('rol', 'gestor')->count();
            $tecnicosCount = User::where('rol', 'tecnico')->count();

            $inspectionsCount = DynamicInspection::whereBetween('dynamic_inspections.completed_at', $range)
                ->where('dynamic_inspections.status', 'completed')
                ->count();

            $trainingsCount = Training::whereBetween('trainings.date', $range)
                ->where('trainings.status', 'completed')
                ->count();

            $masterReportsCount = MasterReport::whereBetween('master_reports.created_at', $range)
                ->whereIn('master_reports.estado', ['finalizado', 'aprobado'])
                ->count();

            $companiesQuery = Empresa::query();
        }

        $companies = $companiesQuery->with('consultora')
            ->withCount([
                'sedes',
                'inspecciones' => fn ($q) => $q->whereBetween('dynamic_inspections.completed_at', $range)->where('dynamic_inspections.status', 'completed'),
                'trainings' => fn ($q) => $q->whereBetween('trainings.date', $range)->where('trainings.status', 'completed'),
                'informes' => fn ($q) => $q->whereBetween('master_reports.created_at', $range)->whereIn('master_reports.estado', ['finalizado', 'aprobado']),
            ])
            ->orderBy('nombre')
            ->get();

        return [
            'consultoraName' => $consultoraName,
            'consultants' => $consultants,
            'kpis' => [
                'empresas_count' => $empresasCount, 'gestores_count' => $gestoresCount, 'tecnicos_count' => $tecnicosCount,
                'checklists_templates_count' => DynamicChecklist::count(), 'inspections_count' => $inspectionsCount,
                'trainings_count' => $trainingsCount, 'master_reports_count' => $masterReportsCount,
            ],
            'companies' => $companies,
            'filters' => ['from' => $from, 'to' => $to, 'consultora_id' => $consultoraId],
        ];
    }

    /**
     * Build Gestor dashboard KPIs, company matrix, efficiency, tactical terminal, and hours tracking.
     */
    public function getGestorDashboardData(User $user, Request $request): array
    {
        $consultoraId = $user->consultora_id;
        $fromRaw = $request->query('from', now()->startOfMonth()->format('Y-m-d'));
        $toRaw = $request->query('to', now()->endOfMonth()->format('Y-m-d'));

        $from = Carbon::parse($fromRaw)->startOfDay();
        $to = Carbon::parse($toRaw)->endOfDay();

        $kpis = [
            'total_companies' => Empresa::where('consultora_id', $consultoraId)->count(),
            'total_inspections' => DynamicInspection::whereBetween('completed_at', [$from, $to])
                ->whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $consultoraId))
                ->completed()
                ->count(),
            'total_findings' => Hallazgo::whereBetween('fecha_deteccion', [$from, $to])
                ->whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $consultoraId))
                ->count(),
            'total_incidents' => Incidente::whereBetween('fecha_ocurrencia', [$from, $to])
                ->whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $consultoraId))
                ->count(),
            'total_reports' => MasterReport::whereBetween('updated_at', [$from, $to])
                ->whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $consultoraId))
                ->whereIn('estado', ['finalizado', 'aprobado'])
                ->count(),
        ];

        $companies = Empresa::where('consultora_id', $consultoraId)
            ->withCount([
                'inspecciones' => fn ($q) => $q->whereBetween('dynamic_inspections.completed_at', [$from, $to])->completed(),
                'hallazgos' => fn ($q) => $q->whereBetween('hallazgos.fecha_deteccion', [$from, $to]),
                'incidentes' => fn ($q) => $q->whereBetween('incidentes.fecha_ocurrencia', [$from, $to]),
                'informes' => fn ($q) => $q->whereBetween('master_reports.updated_at', [$from, $to])->whereIn('master_reports.estado', ['finalizado', 'aprobado']),
                'checklistAssignments as active_assignments_count',
                'sedes as sedes_con_plan_count' => fn ($q) => $q->whereHas('annualPlans', fn ($p) => $p->where('year', now()->year)),
            ])
            ->orderBy('nombre')
            ->get();

        return [
            'kpis' => $kpis,
            'companies' => $companies,
            'efficiency' => $this->calculateConsultoraEfficiency($consultoraId, $from),
            'recentEvents' => $this->getTacticalTerminalEvents($consultoraId),
            'hoursTracking' => $this->calculateHoursTracking($consultoraId, $from, $to),
            'filters' => [
                'from' => $fromRaw,
                'to' => $toRaw,
            ],
        ];
    }

    private function calculateHoursTracking(?int $consultoraId, Carbon $from, Carbon $to): array
    {
        $timeService = new TimeTrackingService;
        $visits = VisitCertificate::with('sede')
            ->whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $consultoraId))
            ->whereBetween('visit_date', [$from, $to])
            ->whereIn('status', ['completed', 'signed'])
            ->get();

        $totalFieldHours = 0.0;
        $totalOfficeHours = 0.0;
        $exceededWarningsCount = 0;

        foreach ($visits as $visit) {
            $fieldHours = $timeService->calculateFieldHours($visit);
            $totalFieldHours += $fieldHours;
            $totalOfficeHours += (float) $visit->horas_gabinete_estimadas;

            if ($visit->start_time && ! $timeService->verifyWorkingHours($visit->sede, $visit->start_time)) {
                $exceededWarningsCount++;

                continue;
            }

            $sede = $visit->sede;
            if ($sede && ! $sede->es_roster_24_7 && $sede->hora_apertura && $sede->hora_cierre) {
                $apertura = $this->parseTime($sede->hora_apertura);
                $cierre = $this->parseTime($sede->hora_cierre);

                if ($apertura && $cierre && $fieldHours > ($cierre->diffInSeconds($apertura) / 3600.0)) {
                    $exceededWarningsCount++;
                }
            }
        }

        return [
            'field_hours' => round($totalFieldHours, 2),
            'office_hours' => round($totalOfficeHours, 2),
            'exceeded_warnings_count' => $exceededWarningsCount,
        ];
    }

    private function parseTime(string $value): ?Carbon
    {
        foreach (['H:i:s', 'H:i'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value);
            } catch (\Exception $e) {
            }
        }

        return null;
    }

    private function getTacticalTerminalEvents(?int $consultoraId)
    {
        $limit = 15;
        $scope = fn ($q) => $q->where('consultora_id', $consultoraId);

        $inspections = DynamicInspection::with(['checklist', 'user', 'sede.empresa'])->whereHas('sede.empresa', $scope)->completed()->latest('completed_at')->limit($limit)->get()
            ->map(fn ($i) => ['time' => $i->completed_at, 'label' => 'INSPECCIÓN', 'color' => 'emerald', 'description' => "{$i->user?->full_name} finalizó '{$i->checklist?->title}' en ".($i->sede?->empresa?->nombre ?? 'Sede desconocida'), 'diffText' => $i->completed_at?->diffForHumans()]);

        $findings = Hallazgo::with(['sede.empresa'])->whereHas('sede.empresa', $scope)->latest('fecha_deteccion')->limit($limit)->get()
            ->map(fn ($h) => ['time' => $h->created_at, 'label' => 'HALLAZGO', 'color' => 'yellow', 'description' => "Nuevo hallazgo '{$h->titulo}' detectado en ".($h->sede?->empresa?->nombre ?? 'Sede desconocida')." (Nivel: {$h->criticidad})", 'diffText' => $h->created_at?->diffForHumans()]);

        $incidents = Incidente::with(['sede.empresa'])->whereHas('sede.empresa', $scope)->latest('fecha_ocurrencia')->limit($limit)->get()
            ->map(fn ($inc) => ['time' => $inc->created_at, 'label' => 'SINIESTRO', 'color' => 'red', 'description' => 'Siniestro reportado en '.($inc->sede?->empresa?->nombre ?? 'Sede desconocida').": {$inc->tipo}", 'diffText' => $inc->created_at?->diffForHumans()]);

        $reports = MasterReport::with(['sede.empresa'])->whereHas('sede.empresa', $scope)->whereIn('estado', ['finalizado', 'aprobado'])->latest('updated_at')->limit($limit)->get()
            ->map(fn ($r) => ['time' => $r->updated_at, 'label' => 'REPORTE', 'color' => 'blue', 'description' => 'Informe Maestro consolidado para '.($r->sede?->empresa?->nombre ?? 'Sede desconocida')." ({$r->periodo})", 'diffText' => $r->updated_at?->diffForHumans()]);

        return collect([])->concat($inspections)->concat($findings)->concat($incidents)->concat($reports)->sortByDesc('time')->take(20)->values();
    }

    private function calculateConsultoraEfficiency(?int $consultoraId, ?Carbon $from = null): array
    {
        $date = $from ?? now();
        $year = $date->year;
        $month = $date->month;
        $scope = fn ($q) => $q->where('consultora_id', $consultoraId);

        $totalPlanned = AnnualPlan::whereHas('sede.empresa', $scope)
            ->where('scheduled_months', 'like', "%{$year}-".str_pad((string) $month, 2, '0', STR_PAD_LEFT).'%')->count();
        $totalDone = DynamicInspection::whereHas('sede.empresa', $scope)
            ->whereYear('completed_at', $year)->whereMonth('completed_at', $month)->completed()->count();

        return [
            'percentage' => $totalPlanned > 0 ? round(($totalDone / $totalPlanned) * 100) : 0,
            'planned' => $totalPlanned,
            'done' => $totalDone,
        ];
    }
}
