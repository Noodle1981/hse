<?php

namespace App\Services;

use App\Models\MasterReport;
use App\Models\Sede;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MasterReportService
{
    public function __construct(private readonly MasterReportComplianceService $complianceService) {}

    public function getIndexData(User $user, Request $request): array
    {
        $query = MasterReport::with(['sede', 'tecnico', 'gestor'])->latest();

        if ($user->isTechnician()) {
            $query->where('user_id', $user->id);
        } elseif (! $user->isSuperAdmin()) {
            $query->whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $user->consultora_id));
        }

        if ($request->status) {
            $query->where('estado', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('sede', fn ($sq) => $sq->where('nombre_sede', 'like', '%'.$request->search.'%'))
                    ->orWhere('titulo', 'like', '%'.$request->search.'%');
            });
        }

        return [
            'reports' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'status']),
        ];
    }

    public function getCreateData(User $user, Request $request): array
    {
        $consultoraId = $user->isSuperAdmin() ? null : $user->consultora_id;

        $sedes = $user->isTechnician()
            ? $user->sedes()->with('empresa')->get()
            : Sede::with('empresa')
                ->when($consultoraId, fn ($q) => $q->whereHas('empresa', fn ($eq) => $eq->where('consultora_id', $consultoraId)))
                ->orderBy('nombre_sede')
                ->get();

        $tecnicos = User::where('rol', User::ROLE_TECNICO)
            ->when($consultoraId, fn ($q) => $q->where('consultora_id', $consultoraId))
            ->get();

        return [
            'sedes' => $sedes,
            'tecnicos' => $tecnicos,
            'initial_sede_id' => $request->sede_id,
            'initial_periodo' => $request->periodo,
            'initial_user_id' => $request->user_id ?? ($user->isTechnician() ? (string) $user->id : ''),
        ];
    }

    public function createReport(User $user, array $validatedData): MasterReport
    {
        return MasterReport::create([
            ...$validatedData,
            'solicitado_por' => $user->id,
            'estado' => $user->isTechnician() ? 'en_redaccion' : 'solicitado',
        ]);
    }

    public function getWizardData(MasterReport $masterReport): array
    {
        $masterReport->load(['sede.empresa', 'tecnico', 'gestor']);

        return [
            'report' => $masterReport,
            'periodData' => $this->aggregatePeriodData($masterReport),
        ];
    }

    public function handleUpdateTransition(User $user, MasterReport $masterReport, Request $request): ?string
    {
        if ($user->isGestor() || $user->isSuperAdmin()) {
            if ($request->action === 'approve') {
                $masterReport->update(['estado' => 'aprobado', 'observacion_revision' => null]);

                return 'Informe maestro aprobado exitosamente.';
            }

            if ($request->action === 'observe') {
                $masterReport->update(['estado' => 'en_redaccion', 'observacion_revision' => $request->observacion_revision]);

                return 'Informe maestro observado y devuelto al técnico.';
            }
        }

        if ($request->finalize) {
            $masterReport->update(['estado' => 'finalizado']);

            return 'Informe finalizado exitosamente.';
        }

        return null;
    }

    public function aggregatePeriodData(MasterReport $report): array
    {
        [$startDate, $endDate] = $this->resolvePeriodRange($report->periodo, $report->frecuencia ?? 'mensual');

        $visits = $report->sede->visitCertificates()
            ->with([
                'checklistCompletions.assignment.checklist',
                'checklistCompletions.assignment.asset.assetType',
                'checklistCompletions.assignment.inspection.asset.assetType',
                'trainings.attendances',
                'hallazgos.fotos',
                'incidentes.fotos',
                'user',
            ])
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->whereIn('status', ['completed', 'signed'])
            ->get();

        $data = [
            'visits' => $visits,
            'checklists' => $visits->flatMap->checklistCompletions,
            'trainings' => $visits->flatMap->trainings,
            'hallazgos' => $visits->flatMap->hallazgos,
            'incidentes' => $visits->flatMap->incidentes,
        ];

        $data['annual_plan_status'] = $this->complianceService->calculateAnnualPlanStatus($report->sede, $startDate, $endDate, $data);
        $data['adhoc_activities'] = $this->complianceService->collectAdhocActivities($report->sede, $startDate, $endDate, $data);

        return $data;
    }

    private function resolvePeriodRange(string $period, string $freq): array
    {
        if ($freq === 'trimestral') {
            $q = (int) substr($period, -1);
            $y = (int) substr($period, 0, 4);
            $startMonth = ($q - 1) * 3 + 1;

            return [Carbon::create($y, $startMonth, 1)->startOfMonth(), Carbon::create($y, $startMonth + 2, 1)->endOfMonth()];
        }

        if ($freq === 'semestral') {
            $s = (int) substr($period, -1);
            $y = (int) substr($period, 0, 4);
            $startMonth = ($s - 1) * 6 + 1;

            return [Carbon::create($y, $startMonth, 1)->startOfMonth(), Carbon::create($y, $startMonth + 5, 1)->endOfMonth()];
        }

        if ($freq === 'anual') {
            $y = (int) substr($period, 0, 4);

            return [Carbon::create($y, 1, 1)->startOfMonth(), Carbon::create($y, 12, 31)->endOfMonth()];
        }

        try {
            return [Carbon::parse($period.'-01')->startOfMonth(), Carbon::parse($period.'-01')->endOfMonth()];
        } catch (\Exception $e) {
            return [now()->startOfMonth(), now()->endOfMonth()];
        }
    }
}
