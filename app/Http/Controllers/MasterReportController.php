<?php

namespace App\Http\Controllers;

use App\Models\MasterReport;
use App\Models\Sede;
use App\Models\User;
use App\Models\DynamicChecklist;
use App\Models\AnnualPlan;
use App\Models\VisitCertificate;
use App\Services\BrandingService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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
            $query->where(function($q) use ($request) {
                $q->whereHas('sede', function($sq) use ($request) {
                    $sq->where('nombre_sede', 'like', '%' . $request->search . '%');
                })->orWhere('titulo', 'like', '%' . $request->search . '%');
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
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'sede_id' => 'required|exists:sedes,id',
            'user_id' => 'required|exists:users,id',
            'titulo' => 'required|string|max:255',
            'periodo' => 'required|string',
            'frecuencia' => 'required|in:mensual,trimestral,semestral,anual',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $report = MasterReport::create([
            ...$request->all(),
            'solicitado_por' => Auth::id(),
            'estado' => $user->isTechnician() ? 'en_redaccion' : 'solicitado',
        ]);

        return redirect()->route('gestor.master-reports.edit', $report->id)
            ->with('message', 'Informe iniciado correctamente.');
    }

    public function edit($id)
    {
        $report = MasterReport::with(['sede.empresa', 'tecnico', 'gestor'])->findOrFail($id);
        $this->authorize('view', $report);
        
        $data = $this->aggregatePeriodData($report);

        return Inertia::render('Gestor/MasterReports/Wizard', [
            'report' => $report,
            'periodData' => $data,
        ]);
    }

    public function update(Request $request, $id)
    {
        $report = MasterReport::findOrFail($id);
        
        $report->update($request->only([
            'resumen_ejecutivo',
            'observaciones_visitas',
            'observaciones_checklists',
            'observaciones_capacitaciones',
            'observaciones_hallazgos_incidentes',
            'observaciones_matriz',
            'datos_anexo',
        ]));

        if ($request->finalize) {
            $report->update(['estado' => 'finalizado']);
            return redirect()->route('gestor.master-reports.index')->with('message', 'Informe finalizado exitosamente.');
        }

        return back()->with('message', 'Borrador guardado.');
    }

    public function preview($id)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        $report   = MasterReport::with(['sede.empresa', 'tecnico', 'gestor'])->findOrFail($id);
        $this->authorize('view', $report);

        $data     = $this->aggregatePeriodData($report);
        $branding = BrandingService::forUser($report->tecnico ?? $report->gestor);
        
        $pdf = Pdf::loadView('pdf.master-report-pdf', [
            'report'   => $report,
            'data'     => $data,
            'branding' => $branding,
        ]);

        return $pdf->stream("Informe_Maestro_{$report->periodo}.pdf");
    }

    private function aggregatePeriodData(MasterReport $report)
    {
        $period = $report->periodo;
        $freq = $report->frecuencia ?? 'mensual';

        if ($freq === 'trimestral') {
            $q = (int)substr($period, -1);
            $y = (int)substr($period, 0, 4);
            $startMonth = ($q - 1) * 3 + 1;
            $startDate = Carbon::create($y, $startMonth, 1)->startOfMonth();
            $endDate = Carbon::create($y, $startMonth + 2, 1)->endOfMonth();
        } elseif ($freq === 'semestral') {
            $s = (int)substr($period, -1);
            $y = (int)substr($period, 0, 4);
            $startMonth = ($s - 1) * 6 + 1;
            $startDate = Carbon::create($y, $startMonth, 1)->startOfMonth();
            $endDate = Carbon::create($y, $startMonth + 5, 1)->endOfMonth();
        } elseif ($freq === 'anual') {
            $y = (int)substr($period, 0, 4);
            $startDate = Carbon::create($y, 1, 1)->startOfMonth();
            $endDate = Carbon::create($y, 12, 31)->endOfMonth();
        } else {
            try {
                $startDate = Carbon::parse($period . '-01')->startOfMonth();
                $endDate = Carbon::parse($period . '-01')->endOfMonth();
            } catch (\Exception $e) {
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
            }
        }

        $visits = $report->sede->visitCertificates()
            ->with([
                'checklistCompletions.assignment.checklist', 
                'trainings', 
                'hallazgos.fotos', 
                'incidentes.fotos',
                'user'
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

        $adhoc = collect();
        
        $data['checklists']->reject(fn($c) => $satisfiedCheckIds->contains($c->id))
            ->each(function($c) use ($adhoc) {
                $adhoc->push([
                    'activity' => $c->assignment?->checklist?->title ?? 'Punto de Control',
                    'category' => 'Inspección',
                    'description' => $c->assignment?->checklist?->description ?? null,
                    'date' => Carbon::parse($c->completed_at)->format('d/m/Y'),
                    'technician' => $c->visitCertificate->technician_name ?? 'N/A'
                ]);
            });

        $data['trainings']->reject(fn($t) => $satisfiedTrainIds->contains($t->id))
            ->each(function($t) use ($adhoc) {
                $adhoc->push([
                    'activity' => $t->title,
                    'category' => 'Capacitación',
                    'description' => $t->description,
                    'date' => Carbon::parse($t->date ?? $t->created_at)->format('d/m/Y'),
                    'technician' => $t->trainer_name ?? 'N/A'
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

        if ($plans->isEmpty()) return [
            'percentage' => 0,
            'programmed' => collect(),
            'total_targets' => 0,
            'achieved' => 0,
            'satisfied_checklist_ids' => collect(),
            'satisfied_training_ids' => collect()
        ];

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
                    if (!$isReport) $totalTargets++;

                    $dt = Carbon::parse($m . '-01');
                    $completion = $this->checkActivityCompletion($plan, $dt->month, $dt->year, $data);
                    
                    if ($completion) {
                        if (!$isReport) $achievedTargets++;
                        if ($plan->category === 'Inspección' && isset($completion->id)) $satisfiedChecklistIds->push($completion->id);
                        if ($plan->category === 'Capacitación' && isset($completion->id)) $satisfiedTrainingIds->push($completion->id);
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
                        'month' => Carbon::parse($m . '-01')->translatedFormat('F'),
                        'status' => $completion ? 'completed' : 'pending',
                        'executed_at' => $executedAt,
                        'technician' => $technician
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
            'satisfied_training_ids' => $satisfiedTrainingIds
        ];
    }

    private function checkActivityCompletion($plan, $month, $year, $data)
    {
        if (stripos($plan->activity_name, 'Visita') !== false) {
            return $data['visits']->first(function($v) use ($month, $year) {
                return $v->visit_date->month == $month && $v->visit_date->year == $year;
            });
        }

        if ($plan->activity_code) {
            if ($plan->category === 'Inspección') {
                $found = $data['checklists']->first(function($c) use ($plan, $month, $year) {
                    $date = Carbon::parse($c->completed_at);
                    return $date->month == $month && $date->year == $year && 
                           (($c->assignment?->checklist?->registry_code ?? '') === $plan->activity_code);
                });
                if ($found) return $found;
            }

            if ($plan->category === 'Capacitación') {
                $found = $data['trainings']->first(function($t) use ($plan, $month, $year) {
                    $date = Carbon::parse($t->date ?? $t->created_at);
                    return $date->month == $month && $date->year == $year && 
                           (str_contains(mb_strtolower($t->title), mb_strtolower($plan->activity_code)));
                });
                if ($found) return $found;
            }
        }

        if ($plan->category === 'Inspección' && $plan->checklist_id) {
            return $data['checklists']->first(function($c) use ($plan, $month, $year) {
                $date = Carbon::parse($c->completed_at);
                return ($c->assignment?->dynamic_checklist_id ?? null) == $plan->checklist_id 
                        && $date->month == $month && $date->year == $year;
            });
        }
        
        if ($plan->category === 'Capacitación') {
            return $data['trainings']->first(function($t) use ($plan, $month, $year) {
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
