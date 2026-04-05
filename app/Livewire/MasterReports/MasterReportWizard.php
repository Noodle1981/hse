<?php

namespace App\Livewire\MasterReports;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MasterReportWizard extends Component
{
    public $mode = 'create'; // create, edit
    public $reportId;
    public $report;

    public $sede_id;
    public $user_id; // Técnico
    public $titulo;
    public $periodo; // YYYY-MM
    public $resumen_ejecutivo;
    public $observaciones_matriz;
    public $observaciones_visitas;
    public $observaciones_checklists;
    public $observaciones_capacitaciones;
    public $observaciones_hallazgos_incidentes;
    public $frecuencia = 'mensual'; // mensual, trimestral, semestral, anual
    public $signature; // Base64 Drawn Signature
    
    public $currentStep = 1;
    public $monthlyData = [];

    protected $rules = [
        'sede_id' => 'required|exists:sedes,id',
        'user_id' => 'required|exists:users,id',
        'titulo' => 'required|string|max:255',
        'periodo' => 'required|string|max:10', // Puede ser YYYY-MM o YYYY-QX
        'frecuencia' => 'required|in:mensual,trimestral,semestral,anual',
    ];

    public function mount($mode = 'create', $reportId = null)
    {
        $this->mode = $mode;
        $this->reportId = $reportId;

        if ($this->mode === 'edit' && $this->reportId) {
            $this->report = \App\Models\MasterReport::findOrFail($this->reportId);
            $this->sede_id = $this->report->sede_id;
            $this->user_id = $this->report->user_id;
            $this->titulo = $this->report->titulo;
            $this->periodo = $this->report->periodo;
            $this->frecuencia = $this->report->frecuencia ?? 'mensual';
            $this->resumen_ejecutivo = $this->report->resumen_ejecutivo;
            $this->observaciones_matriz = $this->report->observaciones_matriz;
            $this->observaciones_visitas = $this->report->observaciones_visitas;
            $this->observaciones_checklists = $this->report->observaciones_checklists;
            $this->observaciones_capacitaciones = $this->report->observaciones_capacitaciones;
            $this->observaciones_hallazgos_incidentes = $this->report->observaciones_hallazgos_incidentes;
            $this->signature = $this->report->datos_anexo['firma_tecnico'] ?? null;
            $this->loadMonthlyData();
        } else {
            $this->periodo = now()->format('Y-m');
            $this->titulo = 'Informe Maestro de Servicio - ' . now()->translatedFormat('F Y');
        }
    }

    public function updatedSedeId() { $this->loadMonthlyData(); }
    public function updatedPeriodo() { $this->loadMonthlyData(); }
    public function updatedFrecuencia() { $this->loadMonthlyData(); }

    public function loadMonthlyData()
    {
        if (!$this->sede_id || !$this->periodo) return;

        $period = $this->periodo;
        $freq = $this->frecuencia;

        if ($freq === 'trimestral') {
            $q = (int)substr($period, -1);
            $y = (int)substr($period, 0, 4);
            $startMonth = ($q - 1) * 3 + 1;
            $startDate = \Carbon\Carbon::create($y, $startMonth, 1)->startOfMonth();
            $endDate = \Carbon\Carbon::create($y, $startMonth + 2, 1)->endOfMonth();
        } elseif ($freq === 'semestral') {
            $s = (int)substr($period, -1);
            $y = (int)substr($period, 0, 4);
            $startMonth = ($s - 1) * 6 + 1;
            $startDate = \Carbon\Carbon::create($y, $startMonth, 1)->startOfMonth();
            $endDate = \Carbon\Carbon::create($y, $startMonth + 5, 1)->endOfMonth();
        } elseif ($freq === 'anual') {
            $y = (int)substr($period, 0, 4);
            $startDate = \Carbon\Carbon::create($y, 1, 1)->startOfMonth();
            $endDate = \Carbon\Carbon::create($y, 12, 31)->endOfMonth();
        } else {
            // Mensual
            try {
                $startDate = \Carbon\Carbon::parse($period . '-01')->startOfMonth();
                $endDate = \Carbon\Carbon::parse($period . '-01')->endOfMonth();
            } catch (\Exception $e) {
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
            }
        }

        $sede = \App\Models\Sede::find($this->sede_id);
        if (!$sede) return;

        $visits = $sede->visitCertificates()
            ->with([
                'checklistCompletions.assignment.checklist', 
                'trainings', 
                'hallazgos.fotos', 
                'incidentes.fotos'
            ])
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->whereIn('status', ['completed', 'signed'])
            ->get();

        $data = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'visits' => $visits,
            'checklists' => $visits->flatMap->checklistCompletions,
            'trainings' => $visits->flatMap->trainings,
            'hallazgos' => $visits->flatMap->hallazgos,
            'incidentes' => $visits->flatMap->incidentes,
        ];

        $data['annual_plan_status'] = $this->calculateAnnualPlanStatus($sede, $startDate->year, $data);
        
        // Unificar Actividades Ad-Hoc (fuera de contrato)
        $satisfiedCheckIds = $data['annual_plan_status']['satisfied_checklist_ids'] ?? collect();
        $satisfiedTrainIds = $data['annual_plan_status']['satisfied_training_ids'] ?? collect();

        $adhoc = collect();
        
        // 1. Checklists Ad-Hoc
        $data['checklists']->reject(fn($c) => $satisfiedCheckIds->contains($c->id))
            ->each(function($c) use ($adhoc) {
                $adhoc->push([
                    'activity' => $c->assignment->checklist->title,
                    'category' => 'Inspección',
                    'description' => $c->assignment->checklist->description,
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
        $this->monthlyData = $data;
    }


    private function calculateAnnualPlanStatus($sede, $year, $data)
    {
        $plans = $sede->annualPlans()->with('checklist')->where('year', $year)->get();
        if ($plans->isEmpty()) return [
            'percentage' => 0,
            'programmed' => collect(),
            'adhoc' => collect()
        ];

        // Definir meses a evaluar según frecuencia
        $monthsToEval = [];
        $start = \Carbon\Carbon::parse($data['startDate'] ?? now());
        $end = \Carbon\Carbon::parse($data['endDate'] ?? now());
        
        $curr = $start->copy();
        while ($curr->lte($end)) {
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
                    // Buscar si se realizó en ese mes específico
                    $monthNum = \Carbon\Carbon::parse("first day of $m $year")->month;
                    $completion = $this->checkActivityCompletion($plan, $monthNum, $year, $data);
                    
                    if ($completion) {
                        $achievedTargets++;
                        if ($plan->category === 'Inspección' && isset($completion->id)) {
                            $satisfiedChecklistIds->push($completion->id);
                        }
                        if ($plan->category === 'Capacitación' && isset($completion->id)) {
                            $satisfiedTrainingIds->push($completion->id);
                        }
                    }
                    
                    $description = $plan->checklist->description ?? null;

                    // Long-term Fallback: If ID is null, try matching by name
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
        if ($plan->category === 'Inspección' && $plan->checklist_id) {
            return $data['checklists']->first(function($c) use ($plan, $month, $year) {
                $date = \Carbon\Carbon::parse($c->completed_at);
                return $c->assignment->dynamic_checklist_id == $plan->checklist_id 
                    && $date->month == $month && $date->year == $year;
            });
        }
        
        if ($plan->category === 'Capacitación') {
            return $data['trainings']->first(function($t) use ($plan, $month, $year) {
                $date = \Carbon\Carbon::parse($t->date ?? $t->created_at);
                return $t->title === $plan->activity_name && $date->month == $month && $date->year == $year;
            });
        }

        if ($plan->category === 'Informe') {
            return true;
        }

        return false;
    }

    public function save()
    {
        $this->validate();

        if ($this->mode === 'create') {
            \App\Models\MasterReport::create([
                'sede_id' => $this->sede_id,
                'user_id' => $this->user_id,
                'solicitado_por' => Auth::id(),
                'titulo' => $this->titulo,
                'periodo' => $this->periodo,
                'frecuencia' => $this->frecuencia,
                'estado' => 'solicitado',
            ]);
            session()->flash('success', 'Informe solicitado exitosamente.');
            return redirect()->route('gestor.master-reports.index');
        } else {
            $datos_anexo = $this->report->datos_anexo ?? [];
            if ($this->signature) {
                $datos_anexo['firma_tecnico'] = $this->signature;
            }

            $this->report->update([
                'resumen_ejecutivo' => $this->resumen_ejecutivo,
                'observaciones_matriz' => $this->observaciones_matriz,
                'observaciones_visitas' => $this->observaciones_visitas,
                'observaciones_checklists' => $this->observaciones_checklists,
                'observaciones_capacitaciones' => $this->observaciones_capacitaciones,
                'observaciones_hallazgos_incidentes' => $this->observaciones_hallazgos_incidentes,
                'datos_anexo' => $datos_anexo,
                'estado' => 'finalizado',
            ]);
            session()->flash('success', 'Informe finalizado exitosamente.');
            return redirect()->route('tecnico.master-reports.index');
        }
    }

    public function render()
    {
        return view('livewire.master-reports.master-report-wizard', [
            'sedes' => \App\Models\Sede::all(),
            'tecnicos' => \App\Models\User::where('rol', \App\Models\User::ROLE_TECNICO)->get(),
        ]);
    }
}
