<?php

namespace App\Http\Controllers;

use App\Models\AnnualPlan;
use App\Models\DynamicChecklist;
use App\Models\DynamicInspection;
use App\Models\Empresa;
use App\Models\MasterReport;
use App\Models\Sede;
use App\Models\Training;
use App\Models\User;
use App\Models\VisitTraining;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        if (! $sedeId && $sedes->isNotEmpty()) {
            $sedeId = $sedes->first()->id;
        }

        $activeSede = Sede::with(['empresa', 'users' => function ($q) {
            $q->where('rol', 'tecnico');
        }])->find($sedeId);

        // 2. Parámetros de Planificación (Por defecto inicia en el mes presente Y-m)
        $contractStart = $request->query('contract_start', now()->format('Y-m'));
        $contractDuration = (int) $request->query('duration', 12);

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
            ->when($request->category, fn ($q) => $q->where('category', $request->category))
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
            'checklistOptions' => DynamicChecklist::select('id', 'title', 'registry_code')
                ->withCount(['questions as has_asset_link' => function ($q) {
                    $q->whereNotNull('vincular_a_metadata')->where('vincular_a_metadata', '!=', '');
                }])
                ->get(),
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
            'duration' => 'required|integer',
            'duration_minutes' => 'nullable|integer',
            'modality' => 'nullable|string|in:presencial,virtual,hibrido',
            'multi_trainer' => 'nullable|boolean',
        ]);

        $startDate = Carbon::createFromFormat('Y-m', $request->contract_start)->startOfMonth();
        $endDate = $startDate->copy()->addMonths($request->duration - 1)->endOfMonth();

        $scheduled = [];
        if ($request->frequency !== 'none') {
            for ($i = 0; $i < $request->duration; $i++) {
                $monthNum = $i + 1;
                $shouldCheck = false;
                if ($request->frequency === 'mensual') {
                    $shouldCheck = true;
                } elseif ($request->frequency === 'bimestral' && $monthNum % 2 === 0) {
                    $shouldCheck = true;
                } elseif ($request->frequency === 'trimestral' && $monthNum % 3 === 0) {
                    $shouldCheck = true;
                }

                if ($shouldCheck) {
                    $scheduled[] = $startDate->copy()->addMonths($i)->format('Y-m');
                }
            }
        }

        DB::transaction(function () use ($request, $startDate, $endDate, $scheduled) {
            AnnualPlan::create([
                'sede_id' => $request->sede_id,
                'period_start' => $startDate,
                'period_end' => $endDate,
                'year' => $startDate->year, // Sincronización explícita del año
                'category' => $request->category,
                'frequency' => $request->frequency,
                'activity_name' => $request->activity_name,
                'activity_code' => $request->activity_code,
                'checklist_id' => in_array($request->category, ['Inspección', 'Capacitación']) ? $request->checklist_id : null,
                'scheduled_months' => $scheduled,
                'duration_minutes' => $request->category === 'Capacitación' ? ($request->duration_minutes ?: 15) : null,
                'modality' => $request->category === 'Capacitación' ? ($request->modality ?: 'presencial') : null,
                'multi_trainer' => $request->category === 'Capacitación' ? (bool) $request->multi_trainer : false,
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
            'frequency' => 'required|in:none,mensual,bimestral,trimestral',
            'duration_minutes' => 'nullable|integer',
            'modality' => 'nullable|string|in:presencial,virtual,hibrido',
            'multi_trainer' => 'nullable|boolean',
        ]);

        $plan = AnnualPlan::findOrFail($id);

        $scheduled = $plan->scheduled_months ?? [];
        if ($request->frequency !== $plan->frequency) {
            $startDate = Carbon::parse($plan->period_start)->startOfMonth();
            $endDate = Carbon::parse($plan->period_end)->endOfMonth();
            $duration = (int) $startDate->diffInMonths($endDate) + 1;

            $scheduled = [];
            if ($request->frequency !== 'none') {
                for ($i = 0; $i < $duration; $i++) {
                    $monthNum = $i + 1;
                    $shouldCheck = false;
                    if ($request->frequency === 'mensual') {
                        $shouldCheck = true;
                    } elseif ($request->frequency === 'bimestral' && $monthNum % 2 === 0) {
                        $shouldCheck = true;
                    } elseif ($request->frequency === 'trimestral' && $monthNum % 3 === 0) {
                        $shouldCheck = true;
                    }

                    if ($shouldCheck) {
                        $scheduled[] = $startDate->copy()->addMonths($i)->format('Y-m');
                    }
                }
            }
        }

        $plan->update([
            'category' => $request->category,
            'activity_name' => $request->activity_name,
            'activity_code' => $request->activity_code,
            'checklist_id' => in_array($request->category, ['Inspección', 'Capacitación']) ? $request->checklist_id : null,
            'frequency' => $request->frequency,
            'scheduled_months' => $scheduled,
            'duration_minutes' => $request->category === 'Capacitación' ? ($request->duration_minutes ?: 15) : null,
            'modality' => $request->category === 'Capacitación' ? ($request->modality ?: 'presencial') : null,
            'multi_trainer' => $request->category === 'Capacitación' ? (bool) $request->multi_trainer : false,
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
        if (! $activeSede) {
            return [];
        }

        $sedeId = $activeSede->id;

        // --- PRE-PROCESAMIENTO DE LOOKUP TABLES (OPTIMIZACIÓN O(R)) ---

        // 1. Mapa de Inspecciones: [checklist_id][month] => count/details
        $insLookup = [];
        DynamicInspection::with('user')
            ->where('sede_id', $sedeId)
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get()
            ->each(function ($inp) use (&$insLookup) {
                $m = $inp->completed_at->format('Y-m');
                $id = $inp->dynamic_checklist_id;
                if (! isset($insLookup[$id][$m])) {
                    $insLookup[$id][$m] = [
                        'count' => 0,
                        'passed' => 0,
                        'failed' => 0,
                        'findings' => 0,
                        'execution_date' => $inp->completed_at->format('d/m/Y'),
                        'history_groups' => [],
                    ];
                }
                $insLookup[$id][$m]['count']++;
                $inp->result === 'Pass' ? $insLookup[$id][$m]['passed']++ : $insLookup[$id][$m]['failed']++;

                $techName = $inp->user ? trim($inp->user->nombres.' '.$inp->user->apellidos) : 'N/D';
                $day = $inp->completed_at->format('Y-m-d');
                $userId = $inp->user_id ?? 0;
                $groupKey = $day.'_'.$userId;

                if (! isset($insLookup[$id][$m]['history_groups'][$groupKey])) {
                    $insLookup[$id][$m]['history_groups'][$groupKey] = [
                        'date' => $inp->completed_at->toIso8601String(),
                        'technician_name' => $techName,
                        'total_checklists' => 0,
                        'passed' => 0,
                        'failed' => 0,
                    ];
                }

                $insLookup[$id][$m]['history_groups'][$groupKey]['total_checklists']++;
                if ($inp->result === 'Pass') {
                    $insLookup[$id][$m]['history_groups'][$groupKey]['passed']++;
                } else {
                    $insLookup[$id][$m]['history_groups'][$groupKey]['failed']++;
                }
            });

        foreach ($insLookup as $id => $months) {
            foreach ($months as $m => $data) {
                $insLookup[$id][$m]['history'] = array_values($data['history_groups'] ?? []);
                unset($insLookup[$id][$m]['history_groups']);
            }
        }

        // 2. Mapa de Capacitaciones: [lowered_title][month] => count/details
        //    Y Mapa por Checklist: [checklist_id][month] => count/details
        $trainLookup = [];
        $trainChecklistLookup = [];

        Training::with(['responsibleUser'])
            ->where('sede_id', $sedeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->withCount('attendances')
            ->get()
            ->each(function ($t) use (&$trainLookup, &$trainChecklistLookup) {
                $m = Carbon::parse($t->date)->format('Y-m');
                $techName = $t->responsibleUser ? trim($t->responsibleUser->nombres.' '.$t->responsibleUser->apellidos) : 'N/D';
                $historyItem = [
                    'date' => Carbon::parse($t->date)->toIso8601String(),
                    'technician_name' => $techName,
                    'asistentes' => $t->attendances_count,
                    'notes' => $t->notes ? strip_tags($t->notes) : null,
                ];

                // Por Nombre
                $title = mb_strtolower($t->title);
                if (! isset($trainLookup[$title][$m])) {
                    $trainLookup[$title][$m] = [
                        'count' => 0,
                        'attendees' => 0,
                        'execution_date' => Carbon::parse($t->date)->format('d/m/Y'),
                        'history' => [],
                    ];
                }
                $trainLookup[$title][$m]['count']++;
                $trainLookup[$title][$m]['attendees'] += $t->attendances_count;
                $trainLookup[$title][$m]['history'][] = $historyItem;

                // Por Checklist
                if ($t->dynamic_checklist_id) {
                    if (! isset($trainChecklistLookup[$t->dynamic_checklist_id][$m])) {
                        $trainChecklistLookup[$t->dynamic_checklist_id][$m] = [
                            'count' => 0,
                            'attendees' => 0,
                            'execution_date' => Carbon::parse($t->date)->format('d/m/Y'),
                            'history' => [],
                        ];
                    }
                    $trainChecklistLookup[$t->dynamic_checklist_id][$m]['count']++;
                    $trainChecklistLookup[$t->dynamic_checklist_id][$m]['attendees'] += $t->attendances_count;
                    $trainChecklistLookup[$t->dynamic_checklist_id][$m]['history'][] = $historyItem;
                }
            });

        // 3. Capacitaciones de Visita
        VisitTraining::whereHas('visit', fn ($q) => $q->where('sede_id', $sedeId)->whereBetween('fecha_visita', [$startDate, $endDate]))
            ->with(['visit.user', 'training', 'attendances'])
            ->get()
            ->each(function ($vt) use (&$trainLookup, &$trainChecklistLookup) {
                $m = $vt->created_at->format('Y-m');

                $user = $vt->visit?->user;
                $techName = $user ? trim($user->nombres.' '.$user->apellidos) : 'N/D';
                $historyItem = [
                    'date' => $vt->created_at->toIso8601String(),
                    'technician_name' => $techName,
                    'asistentes' => $vt->attendances->count(),
                    'notes' => $vt->notes ? strip_tags($vt->notes) : null,
                ];

                // Por Nombre
                $title = mb_strtolower($vt->title);
                if (! isset($trainLookup[$title][$m])) {
                    $trainLookup[$title][$m] = [
                        'count' => 0,
                        'attendees' => 0,
                        'execution_date' => $vt->created_at->format('d/m/Y'),
                        'history' => [],
                    ];
                }
                $trainLookup[$title][$m]['count']++;
                $trainLookup[$title][$m]['attendees'] += $vt->attendances->count();
                $trainLookup[$title][$m]['history'][] = $historyItem;

                // Por Checklist (vía entrenamiento base)
                if ($vt->training?->dynamic_checklist_id) {
                    $cId = $vt->training->dynamic_checklist_id;
                    if (! isset($trainChecklistLookup[$cId][$m])) {
                        $trainChecklistLookup[$cId][$m] = [
                            'count' => 0,
                            'attendees' => 0,
                            'execution_date' => $vt->created_at->format('d/m/Y'),
                            'history' => [],
                        ];
                    }
                    $trainChecklistLookup[$cId][$m]['count']++;
                    $trainChecklistLookup[$cId][$m]['attendees'] += $vt->attendances->count();
                    $trainChecklistLookup[$cId][$m]['history'][] = $historyItem;
                }
            });

        // 4. Informes
        $repLookup = [];
        MasterReport::with('tecnico')
            ->where('sede_id', $sedeId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->each(function ($r) use (&$repLookup) {
                $m = str_contains($r->periodo, '-') ? $r->periodo : $r->created_at->format('Y-m');
                if (! isset($repLookup[$m])) {
                    $repLookup[$m] = [
                        'count' => 0,
                        'execution_date' => $r->created_at->format('d/m/Y'),
                        'history' => [],
                    ];
                }
                $repLookup[$m]['count']++;

                $techName = $r->tecnico ? trim($r->tecnico->nombres.' '.$r->tecnico->apellidos) : 'N/D';
                $repLookup[$m]['history'][] = [
                    'date' => $r->created_at->toIso8601String(),
                    'technician_name' => $techName,
                    'observaciones' => $r->observaciones_visitas ?: 'Sin observaciones',
                    'notes' => $r->resumen_ejecutivo ? strip_tags($r->resumen_ejecutivo) : null,
                ];
            });

        // --- CONSTRUCCIÓN DE MATRIZ (OPTIMIZACIÓN O(N*M)) ---

        $data = [];
        $currentDate = now()->format('Y-m');

        foreach ($plans as $plan) {
            $data[$plan->id] = [];
            $scheduledMonths = $plan->scheduled_months ?? [];

            foreach ($dynamicMonths as $monthValue => $monthInfo) {
                if (! in_array($monthValue, $scheduledMonths)) {
                    $data[$plan->id][$monthValue] = ['status' => 'none', 'count' => 0];

                    continue;
                }

                $count = 0;
                $details = ['passed' => 0, 'failed' => 0, 'findings' => 0, 'attendees' => 0, 'execution_date' => null, 'history' => []];

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
                        $details = array_merge($details, $found);
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
            if (! $plan) {
                continue;
            }
            foreach ($months as $month => $data) {
                if ($data['status'] === 'none') {
                    continue;
                }
                $stats['counts']['total']++;
                if ($data['status'] === 'completed') {
                    $stats['counts']['completed']++;
                }
                if ($data['status'] === 'overdue') {
                    $stats['counts']['overdue']++;
                }
                if ($data['status'] === 'pending') {
                    $stats['counts']['pending']++;
                }
            }
        }
        $stats['global'] = $stats['counts']['total'] > 0 ? round(($stats['counts']['completed'] / $stats['counts']['total']) * 100) : 0;

        return $stats;
    }
}
