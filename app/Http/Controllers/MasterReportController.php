<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMasterReportRequest;
use App\Http\Requests\UpdateMasterReportRequest;
use App\Models\MasterReport;
use App\Models\Sede;
use App\Models\User;
use App\Models\VisitCertificate;
use App\Services\BrandingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MasterReportController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = MasterReport::with(['sede', 'tecnico', 'gestor'])
            ->latest();

        /** @var User $user */
        $user = Auth::user();
        if ($user->isTechnician()) {
            $query->where('user_id', Auth::id());
        }

        if ($request->status) {
            $query->where('estado', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('sede', function ($sq) use ($request) {
                    $sq->where('nombre_sede', 'like', '%'.$request->search.'%');
                })->orWhere('titulo', 'like', '%'.$request->search.'%');
            });
        }

        return Inertia::render('Gestor/MasterReports/Index', [
            'reports' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $sedes = $user->isTechnician()
            ? $user->sedes()->with('empresa')->get()
            : Sede::with('empresa')->orderBy('nombre_sede')->get();

        $tecnicos = User::where('rol', User::ROLE_TECNICO)->get();

        return Inertia::render('Gestor/MasterReports/Create', [
            'sedes' => $sedes,
            'tecnicos' => $tecnicos,
            'initial_sede_id' => $request->sede_id,
            'initial_periodo' => $request->periodo,
            'initial_user_id' => $request->user_id ?? ($user->isTechnician() ? (string)$user->id : ''),
        ]);
    }

    public function store(StoreMasterReportRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $report = MasterReport::create([
            ...$request->validatedWithSafeTime(),
            'solicitado_por' => Auth::id(),
            'estado' => $user->isTechnician() ? 'en_redaccion' : 'solicitado',
        ]);

        return redirect()->route('master-reports.edit', $report->id)
            ->with('message', 'Informe iniciado correctamente.');
    }

    public function show(MasterReport $masterReport)
    {
        $this->authorize('view', $masterReport);

        $masterReport->load(['sede.empresa', 'tecnico', 'gestor']);
        $data = $this->aggregatePeriodData($masterReport);

        return Inertia::render('Gestor/MasterReports/Wizard', [
            'report' => $masterReport,
            'periodData' => $data,
            'readOnly' => true, // Siempre solo-lectura desde la vista "show"
        ]);
    }

    public function edit(MasterReport $masterReport)
    {
        $this->authorize('view', $masterReport);

        $masterReport->load(['sede.empresa', 'tecnico', 'gestor']);
        $data = $this->aggregatePeriodData($masterReport);

        return Inertia::render('Gestor/MasterReports/Wizard', [
            'report' => $masterReport,
            'periodData' => $data,
        ]);
    }

    public function update(UpdateMasterReportRequest $request, MasterReport $masterReport)
    {
        $this->authorize('update', $masterReport);

        $masterReport->update($request->safeData());

        /** @var User $user */
        $user = Auth::user();

        // Procesar acciones de revisión del Gestor / Super Admin
        if ($user->isGestor() || $user->isSuperAdmin()) {
            if ($request->action === 'approve') {
                $masterReport->update([
                    'estado' => 'aprobado',
                    'observacion_revision' => null, // Limpiar observación anterior al aprobar
                ]);

                return redirect()->route('master-reports.index')->with('message', 'Informe maestro aprobado exitosamente.');
            }

            if ($request->action === 'observe') {
                $masterReport->update([
                    'estado' => 'en_redaccion', // Vuelve al técnico
                    'observacion_revision' => $request->observacion_revision,
                ]);

                return redirect()->route('master-reports.index')->with('message', 'Informe maestro observado y devuelto al técnico.');
            }
        }

        // Acción del Técnico
        if ($request->finalize) {
            $masterReport->update(['estado' => 'finalizado']);

            return redirect()->route('master-reports.index')->with('message', 'Informe finalizado exitosamente.');
        }

        return back()->with('message', 'Borrador guardado.');
    }

    public function preview(MasterReport $masterReport)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        $this->authorize('view', $masterReport);

        $masterReport->load(['sede.empresa', 'tecnico', 'gestor']);
        $data = $this->aggregatePeriodData($masterReport);
        $branding = BrandingService::forUser($masterReport->tecnico ?? $masterReport->gestor);

        $pdf = Pdf::loadView('pdf.master-report-pdf', [
            'report' => $masterReport,
            'data' => $data,
            'branding' => $branding,
        ]);

        return $pdf->stream("Informe_Maestro_{$masterReport->periodo}.pdf");
    }

    private function aggregatePeriodData(MasterReport $report)
    {
        $period = $report->periodo;
        $freq = $report->frecuencia ?? 'mensual';

        if ($freq === 'trimestral') {
            $q = (int) substr($period, -1);
            $y = (int) substr($period, 0, 4);
            $startMonth = ($q - 1) * 3 + 1;
            $startDate = Carbon::create($y, $startMonth, 1)->startOfMonth();
            $endDate = Carbon::create($y, $startMonth + 2, 1)->endOfMonth();
        } elseif ($freq === 'semestral') {
            $s = (int) substr($period, -1);
            $y = (int) substr($period, 0, 4);
            $startMonth = ($s - 1) * 6 + 1;
            $startDate = Carbon::create($y, $startMonth, 1)->startOfMonth();
            $endDate = Carbon::create($y, $startMonth + 5, 1)->endOfMonth();
        } elseif ($freq === 'anual') {
            $y = (int) substr($period, 0, 4);
            $startDate = Carbon::create($y, 1, 1)->startOfMonth();
            $endDate = Carbon::create($y, 12, 31)->endOfMonth();
        } else {
            try {
                $startDate = Carbon::parse($period.'-01')->startOfMonth();
                $endDate = Carbon::parse($period.'-01')->endOfMonth();
            } catch (\Exception $e) {
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
            }
        }

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

        $data['annual_plan_status'] = $this->calculateAnnualPlanStatus($report->sede, $startDate, $endDate, $data);

        $satisfiedCheckIds = $data['annual_plan_status']['satisfied_checklist_ids'] ?? collect();
        $satisfiedTrainIds = $data['annual_plan_status']['satisfied_training_ids'] ?? collect();

        $plans = $report->sede->annualPlans()
            ->where('period_start', '<=', $endDate)
            ->where('period_end', '>=', $startDate)
            ->get();

        $adhoc = collect();

        $data['checklists']->reject(fn ($c) => $satisfiedCheckIds->contains($c->id))
            ->each(function ($c) use ($adhoc, $plans) {
                // Si la actividad coincide con alguna programada en el plan anual, NO es ad-hoc (aunque sea una repetición)
                $isProgrammed = $plans->contains(function ($plan) use ($c) {
                    if ($plan->category !== 'Inspección') {
                        return false;
                    }
                    if ($plan->checklist_id && $plan->checklist_id == ($c->assignment?->dynamic_checklist_id ?? null)) {
                        return true;
                    }
                    if ($plan->activity_code && ($c->assignment?->checklist?->registry_code ?? '') === $plan->activity_code) {
                        return true;
                    }
                    if ($plan->activity_name && mb_strtolower($plan->activity_name) === mb_strtolower($c->assignment?->checklist?->title ?? '')) {
                        return true;
                    }

                    return false;
                });

                if ($isProgrammed) {
                    return;
                }

                $adhoc->push([
                    'activity' => $c->assignment?->checklist?->title ?? 'Punto de Control',
                    'category' => 'Inspección',
                    'description' => $c->assignment?->checklist?->description ?? null,
                    'date' => Carbon::parse($c->completed_at)->format('d/m/Y'),
                    'technician' => $c->visitCertificate->technician_name ?? 'N/A',
                ]);
            });

        $data['trainings']->reject(fn ($t) => $satisfiedTrainIds->contains($t->id))
            ->each(function ($t) use ($adhoc, $plans) {
                // Si la capacitación coincide con alguna programada en el plan anual, NO es ad-hoc (aunque sea una repetición)
                $isProgrammed = $plans->contains(function ($plan) use ($t) {
                    if ($plan->category !== 'Capacitación') {
                        return false;
                    }
                    if ($plan->activity_code && str_contains(mb_strtolower($t->title), mb_strtolower($plan->activity_code))) {
                        return true;
                    }
                    if ($plan->activity_name) {
                        $planTitle = mb_strtolower($plan->activity_name);
                        $trainingTitle = mb_strtolower($t->title);
                        if (str_contains($trainingTitle, $planTitle) || str_contains($planTitle, $trainingTitle)) {
                            return true;
                        }
                    }

                    return false;
                });

                if ($isProgrammed) {
                    return;
                }

                $adhoc->push([
                    'activity' => $t->title,
                    'category' => 'Capacitación',
                    'description' => $t->description,
                    'date' => Carbon::parse($t->date ?? $t->created_at)->format('d/m/Y'),
                    'technician' => $t->trainer_name ?? 'N/A',
                ]);
            });

        $data['adhoc_activities'] = $adhoc;

        return $data;
    }

    private function calculateAnnualPlanStatus($sede, $startDate, $endDate, $data)
    {
        $plans = $sede->annualPlans()
            ->with('checklist')
            ->where('period_start', '<=', $endDate)
            ->where('period_end', '>=', $startDate)
            ->get();

        if ($plans->isEmpty()) {
            return [
                'percentage' => 0,
                'programmed' => collect(),
                'total_targets' => 0,
                'achieved' => 0,
                'satisfied_checklist_ids' => collect(),
                'satisfied_training_ids' => collect(),
            ];
        }

        $monthsToEval = [];
        $curr = $startDate->copy();
        while ($curr->lte($endDate)) {
            $monthsToEval[] = $curr->format('Y-m');
            $curr->addMonth();
        }

        $programmed = collect();
        $totalTargets = 0;
        $achievedTargets = 0;
        $satisfiedChecklistIds = collect();
        $satisfiedTrainingIds = collect();

        foreach ($plans as $plan) {
            foreach ($monthsToEval as $m) {
                if (is_array($plan->scheduled_months) && in_array($m, $plan->scheduled_months)) {
                    $isReport = ($plan->category === 'Informe');
                    if (! $isReport) {
                        $totalTargets++;
                    }

                    $dt = Carbon::parse($m.'-01');
                    $completion = $this->checkActivityCompletion($plan, $dt->month, $dt->year, $data);

                    if ($completion) {
                        if (! $isReport) {
                            $achievedTargets++;
                        }
                        if ($plan->category === 'Inspección' && isset($completion->id)) {
                            $satisfiedChecklistIds->push($completion->id);
                        }
                        if ($plan->category === 'Capacitación' && isset($completion->id)) {
                            $satisfiedTrainingIds->push($completion->id);
                        }
                    }

                    $executedAt = null;
                    $technician = null;
                    if ($completion && is_object($completion)) {
                        if ($plan->category === 'Inspección' && isset($completion->completed_at)) {
                            $executedAt = Carbon::parse($completion->completed_at)->format('d/m/Y');
                            $technician = $completion->visitCertificate?->user?->nombres ?? 'N/A';
                        } elseif ($plan->category === 'Capacitación') {
                            $executedAt = Carbon::parse($completion->date ?? $completion->created_at)->format('d/m/Y');
                            $technician = $completion->visitCertificate?->user?->nombres ?? 'N/A';
                        } elseif ($completion instanceof VisitCertificate) {
                            $executedAt = $completion->visit_date->format('d/m/Y');
                            $technician = $completion->user?->nombres ?? 'N/A';
                        }
                    }

                    $programmed->push([
                        'activity' => $plan->activity_name,
                        'code' => $plan->activity_code,
                        'category' => $plan->category,
                        'description' => $plan->checklist->description ?? null,
                        'month' => Carbon::parse($m.'-01')->translatedFormat('F'),
                        'status' => $completion ? 'completed' : 'pending',
                        'executed_at' => $executedAt,
                        'technician' => $technician,
                    ]);
                }
            }
        }

        return [
            'percentage' => $totalTargets > 0 ? round(($achievedTargets / $totalTargets) * 100) : 100,
            'programmed' => $programmed,
            'total_targets' => $totalTargets,
            'achieved' => $achievedTargets,
            'satisfied_checklist_ids' => $satisfiedChecklistIds,
            'satisfied_training_ids' => $satisfiedTrainingIds,
        ];
    }

    private function checkActivityCompletion($plan, $month, $year, $data)
    {
        if (stripos($plan->activity_name, 'Visita') !== false) {
            return $data['visits']->first(function ($v) use ($month, $year) {
                return $v->visit_date->month == $month && $v->visit_date->year == $year;
            });
        }

        if ($plan->activity_code) {
            if ($plan->category === 'Inspección') {
                $found = $data['checklists']->first(function ($c) use ($plan, $month, $year) {
                    $date = Carbon::parse($c->completed_at);

                    return $date->month == $month && $date->year == $year &&
                           (($c->assignment?->checklist?->registry_code ?? '') === $plan->activity_code);
                });
                if ($found) {
                    return $found;
                }
            }

            if ($plan->category === 'Capacitación') {
                $found = $data['trainings']->first(function ($t) use ($plan, $month, $year) {
                    $date = Carbon::parse($t->date ?? $t->created_at);

                    return $date->month == $month && $date->year == $year &&
                           (str_contains(mb_strtolower($t->title), mb_strtolower($plan->activity_code)));
                });
                if ($found) {
                    return $found;
                }
            }
        }

        if ($plan->category === 'Inspección' && $plan->checklist_id) {
            return $data['checklists']->first(function ($c) use ($plan, $month, $year) {
                $date = Carbon::parse($c->completed_at);

                return ($c->assignment?->dynamic_checklist_id ?? null) == $plan->checklist_id
                        && $date->month == $month && $date->year == $year;
            });
        }

        if ($plan->category === 'Capacitación') {
            return $data['trainings']->first(function ($t) use ($plan, $month, $year) {
                $date = Carbon::parse($t->date ?? $t->created_at);
                $planTitle = mb_strtolower($plan->activity_name);
                $trainingTitle = mb_strtolower($t->title);

                return (str_contains($trainingTitle, $planTitle) || str_contains($planTitle, $trainingTitle))
                        && $date->month == $month && $date->year == $year;
            });
        }

        return false;
    }
}
