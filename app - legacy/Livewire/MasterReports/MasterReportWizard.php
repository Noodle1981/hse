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

        // ACTIVACIÓN INVISIBLE: Si viene de un plan anual y es técnico, crear si no existe
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($this->mode === 'create' && request('task_id') && $user && $user->isTechnician()) {
            $plan = \App\Models\AnnualPlan::find(request('task_id'));
            $this->sede_id = request('sede_id');
            
            // Normalizar el periodo a YYYY-MM (en caso de que venga como "April 2026")
            try {
                $rawPeriodo = request('periodo');
                $this->periodo = \Carbon\Carbon::parse($rawPeriodo)->format('Y-m');
            } catch (\Exception $e) {
                $this->periodo = request('periodo');
            }

            if ($plan && $this->sede_id && $this->periodo) {
                // Verificar si ya existe para evitar duplicados
                $existing = \App\Models\MasterReport::where('sede_id', $this->sede_id)
                    ->where('periodo', $this->periodo)
                    ->first();

                if ($existing) {
                    $this->reportId = $existing->id;
                } else {
                    // Normalizar frecuencia: 'none' no es válida para MasterReport
                    $frecuenciaPlan = $plan->frequency ?? 'mensual';
                    if ($frecuenciaPlan === 'none') $frecuenciaPlan = 'mensual';

                    $newReport = \App\Models\MasterReport::create([
                        'sede_id' => $this->sede_id,
                        'user_id' => Auth::id(),
                        'solicitado_por' => Auth::id(),
                        'titulo' => 'Informe Maestro de Servicio - ' . ($plan->sede?->nombre_sede ?? '') . ' - ' . \Carbon\Carbon::parse($this->periodo . '-01')->translatedFormat('F Y'),
                        'periodo' => $this->periodo,
                        'frecuencia' => $frecuenciaPlan,
                        'estado' => 'en_redaccion',
                    ]);
                    $this->reportId = $newReport->id;
                }
                $this->mode = 'edit';
            }
        }

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
            // Flow estándar de creación manual (Gestor)
            $this->sede_id = request('sede_id', $this->sede_id);
            $this->periodo = request('periodo', now()->format('Y-m'));

            /** @var \App\Models\User $user */
            $user = Auth::user();
            $this->user_id = ($user && $user->isTechnician()) ? Auth::id() : null;

            if ($this->sede_id) {
                $sede = \App\Models\Sede::find($this->sede_id);
                $this->titulo = 'Informe Maestro de Servicio - ' . ($sede ? $sede->nombre_sede . ' - ' : '') . \Carbon\Carbon::parse($this->periodo . '-01')->translatedFormat('F Y');
                $this->loadMonthlyData();
            } else {
                $this->titulo = 'Informe Maestro de Servicio - ' . now()->translatedFormat('F Y');
            }
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
        $this->monthlyData = $data;
    }


    private function calculateAnnualPlanStatus($sede, $year, $data)
    {
        // El plan anual ahora se filtra por rango de fechas (period_start/end)
        $startOfMonth = \Carbon\Carbon::parse($data['startDate'] ?? now())->startOfMonth();
        $endOfMonth = \Carbon\Carbon::parse($data['endDate'] ?? now())->endOfMonth();

        $plans = $sede->annualPlans()
            ->with('checklist')
            ->where('period_start', '<=', $endOfMonth)
            ->where('period_end', '>=', $startOfMonth)
            ->get();

        if ($plans->isEmpty()) return [
            'percentage' => 0,
            'programmed' => collect(),
            'adhoc' => collect()
        ];

        // Definir meses a evaluar en formato YYYY-MM para coincidir con DB
        $monthsToEval = [];
        $curr = $startOfMonth->copy();
        while ($curr->lte($endOfMonth)) {
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
                // Verificar en el array de meses programados (JSON en DB)
                if (is_array($plan->scheduled_months) && in_array($m, $plan->scheduled_months)) {
                    // El informe NO cuenta para el KPI de ejecución técnica según requerimiento
                    $isReport = ($plan->category === 'Informe');
                    if (!$isReport) {
                        $totalTargets++;
                    }

                    // Extraer mes/año del formato YYYY-MM
                    $dt = \Carbon\Carbon::parse($m . '-01');
                    $monthNum = $dt->month;
                    $yearNum = $dt->year;

                    $completion = $this->checkActivityCompletion($plan, $monthNum, $yearNum, $data);
                    
                    if ($completion) {
                        if (!$isReport) {
                            $achievedTargets++;
                        }
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

                    // Determinar fecha y técnico de ejecución si está completado
                    $executedAt = null;
                    $technician = null;
                    if ($completion && is_object($completion)) {
                        // Es una Inspección (VisitChecklistCompletion)
                        if ($plan->category === 'Inspección' && isset($completion->completed_at)) {
                            $executedAt = \Carbon\Carbon::parse($completion->completed_at)->format('d/m/Y');
                            $technician = $completion->visitCertificate?->user?->nombres ?? 'N/A';
                        } 
                        // Es una Capacitación (VisitTraining)
                        elseif ($plan->category === 'Capacitación') {
                            $executedAt = \Carbon\Carbon::parse($completion->date ?? $completion->created_at)->format('d/m/Y');
                            $technician = $completion->visitCertificate?->user?->nombres ?? 'N/A';
                        }
                        // Es una Visita genérica
                        elseif ($completion instanceof \App\Models\VisitCertificate) {
                            $executedAt = $completion->visit_date->format('d/m/Y');
                            $technician = $completion->user?->nombres ?? 'N/A';
                        }
                    }
                    
                    $programmed->push([
                        'activity' => $plan->activity_name,
                        'code' => $plan->activity_code,
                        'category' => $plan->category,
                        'description' => $description,
                        'month' => \Carbon\Carbon::parse($m . '-01')->translatedFormat('F'),
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
        // 1. Caso Especial: Si el nombre de la actividad contiene "Visita", chequear contra las Visitas del mes
        if (stripos($plan->activity_name, 'Visita') !== false) {
            return $data['visits']->first(function($v) use ($month, $year) {
                return $v->visit_date->month == $month && $v->visit_date->year == $year;
            });
        }

        // 2. Coincidencia por código de actividad (Piedra angular de la sincronización)
        if ($plan->activity_code) {
            // Para Inspecciones: comparar con registry_code del checklist
            if ($plan->category === 'Inspección') {
                $found = $data['checklists']->first(function($c) use ($plan, $month, $year) {
                    $date = \Carbon\Carbon::parse($c->completed_at);
                    return $date->month == $month && $date->year == $year && 
                           (($c->assignment?->checklist?->registry_code ?? '') === $plan->activity_code);
                });
                if ($found) return $found;
            }

            // Para Capacitaciones: buscar código en el título
            if ($plan->category === 'Capacitación') {
                $found = $data['trainings']->first(function($t) use ($plan, $month, $year) {
                    $date = \Carbon\Carbon::parse($t->date ?? $t->created_at);
                    return $date->month == $month && $date->year == $year && 
                           (str_contains(mb_strtolower($t->title), mb_strtolower($plan->activity_code)));
                });
                if ($found) return $found;
            }
        }

        // 3. Fallback por IDs técnicos (Asignaciones directas)
        if ($plan->category === 'Inspección' && $plan->checklist_id) {
            return $data['checklists']->first(function($c) use ($plan, $month, $year) {
                $date = \Carbon\Carbon::parse($c->completed_at);
                return ($c->assignment?->dynamic_checklist_id ?? null) == $plan->checklist_id 
                        && $date->month == $month && $date->year == $year;
            });
        }
        
        // 4. Fallback por Nombre (Búsqueda flexible)
        if ($plan->category === 'Capacitación') {
            return $data['trainings']->first(function($t) use ($plan, $month, $year) {
                $date = \Carbon\Carbon::parse($t->date ?? $t->created_at);
                $planTitle = mb_strtolower($plan->activity_name);
                $trainingTitle = mb_strtolower($t->title);
                return (str_contains($trainingTitle, $planTitle) || str_contains($planTitle, $trainingTitle)) 
                        && $date->month == $month && $date->year == $year;
            });
        }

        // 5. Informe: Se autogestiona si existe el reporte físico para ese periodo
        if ($plan->category === 'Informe') {
            return \App\Models\MasterReport::where('sede_id', $this->sede_id)
                ->where('periodo', "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT))
                ->exists();
        }

        return false;
    }

    public function save()
    {
        $this->validate();

        if ($this->mode === 'create') {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $isTechnician = $user->isTechnician();
            
            \App\Models\MasterReport::create([
                'sede_id' => $this->sede_id,
                'user_id' => $this->user_id,
                'solicitado_por' => Auth::id(),
                'titulo' => $this->titulo,
                'periodo' => $this->periodo,
                'frecuencia' => $this->frecuencia,
                'estado' => $isTechnician ? 'en_redaccion' : 'solicitado',
            ]);
            session()->flash('success', $isTechnician ? 'Informe iniciado exitosamente.' : 'Informe solicitado exitosamente.');
            
            return redirect()->route($isTechnician ? 'tecnico.master-reports.index' : 'gestor.master-reports.index');
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
