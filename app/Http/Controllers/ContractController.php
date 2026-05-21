<?php

namespace App\Http\Controllers;

use App\Models\AnnualPlan;
use App\Models\Sede;
use App\Models\Empresa;
use App\Models\DynamicChecklist;
use App\Models\DynamicInspection;
use App\Models\Training;
use App\Models\VisitTraining;
use App\Models\MasterReport;
use App\Models\Incidente;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // 1. Contexto de filtrado
        $availableSedes = $user->isAdmin()
            ? Sede::select('id', 'nombre_sede', 'empresa_id')->orderBy('nombre_sede')->get()
            : $user->sedes()->select('sedes.id', 'sedes.nombre_sede', 'sedes.empresa_id')->orderBy('nombre_sede')->get();

        $companies = Empresa::select('id', 'nombre')
            ->whereIn('id', $availableSedes->pluck('empresa_id'))
            ->orderBy('nombre')
            ->get();

        $companyId = $request->query('company_id', $companies->first()?->id);
        $sedes = $availableSedes->where('empresa_id', $companyId)->values();
        
        $sedeId = $request->query('sede_id');
        if (!$sedeId && $sedes->isNotEmpty()) {
            $sedeId = $sedes->first()->id;
        }

        $activeSede = Sede::with(['empresa', 'users' => function ($q) {
            $q->where('rol', 'tecnico');
        }])->find($sedeId);

        // 2. Parámetros de Planificación
        $contractStart = $request->query('contract_start', date('Y-m'));
        $contractDuration = (int) $request->query('duration', 12);

        // Si no se pasó inicio, intentar detectar del último plan
        if (!$request->has('contract_start') && $sedeId) {
            $latestPlan = AnnualPlan::where('sede_id', $sedeId)->latest('id')->first();
            if ($latestPlan && $latestPlan->period_start) {
                $contractStart = $latestPlan->period_start->format('Y-m');
                $contractDuration = (int) $latestPlan->period_start->diffInMonths($latestPlan->period_end) + 1;
            }
        }

        // 3. Generación de Meses Dinámicos
        $dynamicMonths = [];
        try {
            $start = Carbon::createFromFormat('Y-m', $contractStart)->startOfMonth();
        } catch (\Exception $e) {
            $start = now()->startOfMonth();
            $contractStart = $start->format('Y-m');
        }

        for ($i = 0; $i < $contractDuration; $i++) {
            $current = $start->copy()->addMonths($i);
            $dynamicMonths[$current->format('Y-m')] = [
                'name' => $current->translatedFormat('F'),
                'short' => $current->translatedFormat('M'),
                'year' => $current->format('Y'),
                'month' => $current->month,
            ];
        }

        // 4. Datos de Cumplimiento
        $plans = AnnualPlan::where('sede_id', $sedeId)
            ->when($request->category, fn($q) => $q->where('category', $request->category))
            ->with('checklist')
            ->orderBy('category')
            ->get();

        $complianceData = $this->calculateComplianceData($activeSede, $plans, $dynamicMonths, $start, $start->copy()->addMonths($contractDuration - 1)->endOfMonth());
        $efficiency = $this->calculateEfficiencyMetrics($complianceData, $plans);

        return Inertia::render('Gestor/Contracts/Index', [
            'companies' => $companies,
            'sedes' => $sedes,
            'activeSede' => $activeSede,
            'plans' => $plans,
            'dynamicMonths' => $dynamicMonths,
            'complianceData' => $complianceData,
            'efficiency' => $efficiency,
            'filters' => [
                'company_id' => $companyId,
                'sede_id' => $sedeId,
                'contract_start' => $contractStart,
                'duration' => $contractDuration,
                'category' => $request->category,
            ],
            'checklistOptions' => DynamicChecklist::select('id', 'title', 'registry_code')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'sede_id' => 'required|exists:sedes,id',
            'activity_name' => 'required|string|max:255',
            'category' => 'required|in:Capacitación,Inspección,Informe',
            'frequency' => 'required|in:none,mensual,bimestral,trimestral',
            'contract_start' => 'required|string',
            'duration' => 'required|integer'
        ]);

        $startDate = Carbon::createFromFormat('Y-m', $request->contract_start)->startOfMonth();
        $endDate = $startDate->copy()->addMonths($request->duration - 1)->endOfMonth();

        $scheduled = [];
        if ($request->frequency !== 'none') {
            for ($i = 0; $i < $request->duration; $i++) {
                $monthNum = $i + 1;
                $shouldCheck = false;
                if ($request->frequency === 'mensual') $shouldCheck = true;
                elseif ($request->frequency === 'bimestral' && $monthNum % 2 === 0) $shouldCheck = true;
                elseif ($request->frequency === 'trimestral' && $monthNum % 3 === 0) $shouldCheck = true;

                if ($shouldCheck) {
                    $scheduled[] = $startDate->copy()->addMonths($i)->format('Y-m');
                }
            }
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $startDate, $endDate, $scheduled) {
            AnnualPlan::create([
                'sede_id' => $request->sede_id,
                'period_start' => $startDate,
                'period_end' => $endDate,
                'year' => $startDate->year, // Sincronización explícita del año
                'category' => $request->category,
                'frequency' => $request->category === 'Informe' ? $request->frequency : 'none',
                'activity_name' => $request->activity_name,
                'activity_code' => $request->activity_code,
                'checklist_id' => in_array($request->category, ['Inspección', 'Capacitación']) ? $request->checklist_id : null,
                'scheduled_months' => $scheduled
            ]);
        });

        return back()->with('message', 'Actividad registrada.');
    }

    public function toggleMonth(Request $request, $id)
    {
        $plan = AnnualPlan::findOrFail($id);
        $monthValue = $request->month;
        $scheduled = $plan->scheduled_months ?? [];

        if (in_array($monthValue, $scheduled)) {
            $scheduled = array_values(array_diff($scheduled, [$monthValue]));
            $dates = $plan->scheduled_dates ?? [];
            unset($dates[$monthValue]);
            $plan->scheduled_dates = $dates;
        } else {
            $scheduled[] = $monthValue;
            sort($scheduled);
        }

        $plan->scheduled_months = $scheduled;
        $plan->save();

        return back();
    }

    public function updateDate(Request $request, $id)
    {
        $plan = AnnualPlan::findOrFail($id);
        $monthValue = $request->month;
        $dateValue = $request->date;
        
        $dates = $plan->scheduled_dates ?? [];
        if ($dateValue) {
            $dates[$monthValue] = $dateValue;
        } else {
            unset($dates[$monthValue]);
        }

        $plan->scheduled_dates = $dates;
        $plan->save();

        return back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'activity_name' => 'required|string|max:255',
            'category' => 'required|in:Capacitación,Inspección,Informe',
            'activity_code' => 'nullable|string|max:255',
        ]);

        $plan = AnnualPlan::findOrFail($id);
        $plan->update([
            'category' => $request->category,
            'activity_name' => $request->activity_name,
            'activity_code' => $request->activity_code,
            'checklist_id' => in_array($request->category, ['Inspección', 'Capacitación']) ? $request->checklist_id : null,
        ]);

        return back()->with('message', 'Actividad actualizada.');
    }

    public function destroy($id)
    {
        AnnualPlan::findOrFail($id)->delete();
        return back()->with('message', 'Actividad eliminada.');
    }

    private function calculateComplianceData($activeSede, $plans, $dynamicMonths, $startDate, $endDate)
    {
        if (!$activeSede) return [];

        $sedeId = $activeSede->id;

        // --- PRE-PROCESAMIENTO DE LOOKUP TABLES (OPTIMIZACIÓN O(R)) ---
        
        // 1. Mapa de Inspecciones: [checklist_id][month] => count/details
        $insLookup = [];
        DynamicInspection::where('sede_id', $sedeId)
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get()
            ->each(function($inp) use (&$insLookup) {
                $m = $inp->completed_at->format('Y-m');
                $id = $inp->dynamic_checklist_id;
                if (!isset($insLookup[$id][$m])) {
                    $insLookup[$id][$m] = ['count' => 0, 'passed' => 0, 'failed' => 0, 'findings' => 0, 'execution_date' => $inp->completed_at->format('d/m/Y')];
                }
                $insLookup[$id][$m]['count']++;
                $inp->result === 'Pass' ? $insLookup[$id][$m]['passed']++ : $insLookup[$id][$m]['failed']++;
                // Nota: findings_count podría ser cargado ávidamente si es necesario
            });

        // 2. Mapa de Capacitaciones: [lowered_title][month] => count/details
        //    Y Mapa por Checklist: [checklist_id][month] => count/details
        $trainLookup = [];
        $trainChecklistLookup = [];

        Training::where('sede_id', $sedeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->withCount('attendances')
            ->get()
            ->each(function($t) use (&$trainLookup, &$trainChecklistLookup) {
                $m = Carbon::parse($t->date)->format('Y-m');
                
                // Por Nombre
                $title = mb_strtolower($t->title);
                if (!isset($trainLookup[$title][$m])) $trainLookup[$title][$m] = ['count' => 0, 'attendees' => 0, 'execution_date' => Carbon::parse($t->date)->format('d/m/Y')];
                $trainLookup[$title][$m]['count']++;
                $trainLookup[$title][$m]['attendees'] += $t->attendances_count;

                // Por Checklist
                if ($t->dynamic_checklist_id) {
                    if (!isset($trainChecklistLookup[$t->dynamic_checklist_id][$m])) $trainChecklistLookup[$t->dynamic_checklist_id][$m] = ['count' => 0, 'attendees' => 0, 'execution_date' => Carbon::parse($t->date)->format('d/m/Y')];
                    $trainChecklistLookup[$t->dynamic_checklist_id][$m]['count']++;
                    $trainChecklistLookup[$t->dynamic_checklist_id][$m]['attendees'] += $t->attendances_count;
                }
            });

        // 3. Capacitaciones de Visita
        VisitTraining::whereHas('visit', fn($q) => $q->where('sede_id', $sedeId)->whereBetween('fecha_visita', [$startDate, $endDate]))
            ->with('training')
            ->get()
            ->each(function($vt) use (&$trainLookup, &$trainChecklistLookup) {
                $m = $vt->created_at->format('Y-m');
                
                // Por Nombre
                $title = mb_strtolower($vt->title);
                if (!isset($trainLookup[$title][$m])) $trainLookup[$title][$m] = ['count' => 0, 'attendees' => 0, 'execution_date' => $vt->created_at->format('d/m/Y')];
                $trainLookup[$title][$m]['count']++;
                $trainLookup[$title][$m]['attendees'] += $vt->attendees_count;

                // Por Checklist (vía entrenamiento base)
                if ($vt->training?->dynamic_checklist_id) {
                    $cId = $vt->training->dynamic_checklist_id;
                    if (!isset($trainChecklistLookup[$cId][$m])) $trainChecklistLookup[$cId][$m] = ['count' => 0, 'attendees' => 0, 'execution_date' => $vt->created_at->format('d/m/Y')];
                    $trainChecklistLookup[$cId][$m]['count']++;
                    $trainChecklistLookup[$cId][$m]['attendees'] += $vt->attendees_count;
                }
            });

        // 4. Informes
        $repLookup = MasterReport::where('sede_id', $sedeId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(fn($r) => str_contains($r->periodo, '-') ? $r->periodo : $r->created_at->format('Y-m'))
            ->map(fn($group) => ['count' => $group->count(), 'execution_date' => $group->first()->created_at->format('d/m/Y')]);

        // --- CONSTRUCCIÓN DE MATRIZ (OPTIMIZACIÓN O(N*M)) ---
        
        $data = [];
        $currentDate = now()->format('Y-m');

        foreach ($plans as $plan) {
            $data[$plan->id] = [];
            $scheduledMonths = $plan->scheduled_months ?? [];

            foreach ($dynamicMonths as $monthValue => $monthInfo) {
                if (!in_array($monthValue, $scheduledMonths)) {
                    $data[$plan->id][$monthValue] = ['status' => 'none', 'count' => 0];
                    continue;
                }

                $count = 0; 
                $details = ['passed' => 0, 'failed' => 0, 'findings' => 0, 'attendees' => 0, 'execution_date' => null];
                
                if ($plan->category === 'Inspección' && $plan->checklist_id) {
                    $found = $insLookup[$plan->checklist_id][$monthValue] ?? null;
                    if ($found) {
                        $count = $found['count'];
                        $details = array_merge($details, $found);
                    }
                } elseif ($plan->category === 'Capacitación') {
                    if ($plan->checklist_id) {
                        $found = $trainChecklistLookup[$plan->checklist_id][$monthValue] ?? null;
                    } else {
                        $title = mb_strtolower($plan->activity_name);
                        $found = $trainLookup[$title][$monthValue] ?? null;
                    }
                    
                    if ($found) {
                        $count = $found['count'];
                        $details = array_merge($details, $found);
                    }
                } elseif ($plan->category === 'Informe') {
                    $found = $repLookup[$monthValue] ?? null;
                    if ($found) {
                        $count = $found['count'];
                        $details['execution_date'] = $found['execution_date'];
                    }
                }

                if ($count > 0) {
                    $data[$plan->id][$monthValue] = ['status' => 'completed', 'count' => $count, 'details' => $details];
                } else {
                    $specificDate = ($plan->scheduled_dates ?? [])[$monthValue] ?? null;
                    $isOverdue = $specificDate ? Carbon::parse($specificDate)->isPast() : ($monthValue < $currentDate);
                    $data[$plan->id][$monthValue] = ['status' => $isOverdue ? 'overdue' : 'pending', 'count' => 0];
                }
            }
        }
        return $data;
    }

    private function calculateEfficiencyMetrics($complianceData, $plans)
    {
        $stats = ['global' => 0, 'inspeccion' => 0, 'capacitacion' => 0, 'informe' => 0, 'counts' => ['total' => 0, 'completed' => 0, 'overdue' => 0, 'pending' => 0]];
        foreach ($complianceData as $planId => $months) {
            $plan = $plans->firstWhere('id', $planId);
            if (!$plan) continue;
            foreach ($months as $month => $data) {
                if ($data['status'] === 'none') continue;
                $stats['counts']['total']++;
                if ($data['status'] === 'completed') $stats['counts']['completed']++;
                if ($data['status'] === 'overdue') $stats['counts']['overdue']++;
                if ($data['status'] === 'pending') $stats['counts']['pending']++;
            }
        }
        $stats['global'] = $stats['counts']['total'] > 0 ? round(($stats['counts']['completed'] / $stats['counts']['total']) * 100) : 0;
        return $stats;
    }
}
