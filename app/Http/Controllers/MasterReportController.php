<?php

namespace App\Http\Controllers;

use App\Models\MasterReport;
use App\Models\Sede;
use App\Services\BrandingService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MasterReportController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        return view('master-reports.index');
    }

    public function create()
    {
        return view('master-reports.create');
    }

    public function edit($id)
    {
        $report = MasterReport::findOrFail($id);
        $this->authorize('view', $report);
        
        return view('master-reports.edit', compact('id'));
    }

    public function loading($id)
    {
        $report = MasterReport::with('sede')->findOrFail($id);
        $this->authorize('view', $report);

        return view('master-reports.loading', compact('report'));
    }

    public function preview($id)
    {
        // Aumentar límites para procesar múltiples imágenes en DomPDF sin causar Error 500
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        $report   = MasterReport::with(['sede.empresa', 'tecnico', 'gestor'])->findOrFail($id);
        $this->authorize('view', $report);

        $data     = $this->aggregatePeriodData($report);
        $branding = BrandingService::forUser($report->tecnico ?? $report->gestor);
        
        // Unificar Actividades Ad-Hoc (fuera de contrato)
        $satisfiedCheckIds = $data['annual_plan_status']['satisfied_checklist_ids'] ?? collect();
        $satisfiedTrainIds = $data['annual_plan_status']['satisfied_training_ids'] ?? collect();

        $adhoc = collect();
        
        // 1. Checklists Ad-Hoc
        $data['checklists']->reject(fn($c) => $satisfiedCheckIds->contains($c->id))
            ->each(function($c) use ($adhoc) {
                $adhoc->push([
                    'activity' => $c->assignment?->checklist?->title ?? 'Punto de Control',
                    'category' => 'Inspección',
                    'description' => $c->assignment?->checklist?->description ?? null,
                    'date' => \Carbon\Carbon::parse($c->completed_at)->format('d/m/Y'),
                    'technician' => $c->visitCertificate->technician_name ?? 'N/A'
                ]);
            });

        // 2. Capacitaciones Ad-Hoc
        $data['trainings']->reject(fn($t) => $satisfiedTrainIds->contains($t->id))
            ->each(function($t) use ($adhoc) {
                $adhoc->push([
                    'activity' => $t->title,
                    'category' => 'Capacitación',
                    'description' => $t->description,
                    'date' => \Carbon\Carbon::parse($t->date ?? $t->created_at)->format('d/m/Y'),
                    'technician' => $t->trainer_name ?? 'N/A'
                ]);
            });

        $data['adhoc_activities'] = $adhoc;

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
            // Mensual o fallback
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
                'incidentes.fotos'
            ])
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->whereIn('status', ['completed', 'signed'])
            ->get();

        // Calcular cumplimiento del plan anual (reutilizando lógica similar al Wizard)
        $plans = $report->sede->annualPlans()->with('checklist')->where('year', $startDate->year)->get();
        $monthsToEval = [];
        $curr = $startDate->copy();
        while ($curr->lte($endDate)) {
            $monthsToEval[] = strtolower($curr->format('F'));
            $curr->addMonth();
        }

        $programmed = collect();
        $totalTargets = 0;
        $achievedTargets = 0;
        $satisfiedChecklistIds = collect();
        $satisfiedTrainingIds = collect();

        foreach ($plans as $plan) {
            foreach ($monthsToEval as $m) {
                if ($plan->$m) {
                    $totalTargets++;
                    $monthNum = Carbon::parse("first day of $m {$startDate->year}")->month;
                    
                    // Check Completion
                    $completion = null;
                    if ($plan->category === 'Inspección' && $plan->checklist_id) {
                        $completion = $visits->flatMap->checklistCompletions->first(function($c) use ($plan, $monthNum, $startDate) {
                            $date = Carbon::parse($c->completed_at);
                            return ($c->assignment?->dynamic_checklist_id ?? null) == $plan->checklist_id 
                                && $date->month == $monthNum && $date->year == $startDate->year;
                        });
                    } elseif ($plan->category === 'Capacitación') {
                        $isDone = $visits->flatMap->trainings->contains(function($t) use ($plan, $monthNum, $startDate) {
                            $date = Carbon::parse($t->date ?? $t->created_at);
                            return $t->title === $plan->activity_name && $date->month == $monthNum && $date->year == $startDate->year;
                        });
                    } elseif ($plan->category === 'Informe') {
                        $isDone = true;
                    }

                    if ($isDone) $achievedTargets++;
                    
                    $description = $plan->checklist->description ?? null;
                    if (!$description && $plan->category === 'Inspección') {
                        $fallback = \App\Models\DynamicChecklist::where('title', $plan->activity_name)->first();
                        $description = $fallback->description ?? null;
                    }

                    $programmed->push([
                        'activity' => $plan->activity_name,
                        'code' => $plan->activity_code,
                        'category' => $plan->category,
                        'description' => $description,
                        'month' => ucfirst($m),
                        'status' => $completion ? 'completed' : 'pending'
                    ]);
                }
            }
        }

        return [
            'visits' => $visits,
            'checklists' => $visits->flatMap->checklistCompletions,
            'trainings' => $visits->flatMap->trainings,
            'hallazgos' => $visits->flatMap->hallazgos,
            'incidentes' => $visits->flatMap->incidentes,
            'annual_plan_status' => [
                'percentage' => $totalTargets > 0 ? round(($achievedTargets / $totalTargets) * 100) : 100,
                'programmed' => $programmed,
                'total_targets' => $totalTargets,
                'achieved' => $achievedTargets,
                'satisfied_checklist_ids' => $satisfiedChecklistIds,
            'satisfied_training_ids' => $satisfiedTrainingIds
            ]
        ];
    }


}
