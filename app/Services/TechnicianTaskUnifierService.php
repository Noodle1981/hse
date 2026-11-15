<?php

namespace App\Services;

use App\Models\MasterReport;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TechnicianTaskUnifierService
{
    public function buildUnifiedInspections(Collection $contractInspections, Collection $allAssignments, Collection $completedInspectionsAll): Collection
    {
        $unified = collect();

        foreach ($contractInspections as $plan) {
            $matchingAssignments = $allAssignments->where('dynamic_checklist_id', $plan->checklist_id)->where('sede_id', $plan->sede_id);
            $matchingCompleted = $completedInspectionsAll->where('dynamic_checklist_id', $plan->checklist_id)->where('sede_id', $plan->sede_id);
            $completedTotal = $matchingCompleted->count();
            $completedPassed = $matchingCompleted->where('result', 'Pass')->count();

            $key = $plan->checklist_id.'-'.$plan->sede_id;
            $unified->put($key, [
                'id' => $plan->id,
                'type' => 'inspection',
                'title' => $plan->checklist?->title ?? $plan->activity_name,
                'sede' => $plan->sede?->nombre_sede,
                'sede_id' => $plan->sede_id,
                'empresa' => $plan->sede?->empresa?->nombre,
                'due_date' => Carbon::now()->endOfMonth(),
                'status' => $this->resolveGroupStatus($matchingAssignments),
                'checklist_id' => $plan->checklist_id,
                'source' => 'contract',
                'registry_code' => $plan->checklist?->registry_code ?? $plan->activity_code ?? ('AN-'.$plan->id),
                'checklist_count' => $matchingAssignments->count(),
                'completed_count' => $completedTotal,
                'passed_count' => $completedPassed,
                'compliance_pct' => $completedTotal > 0 ? round(($completedPassed / $completedTotal) * 100) : null,
            ]);
        }

        foreach ($allAssignments as $assignment) {
            $key = $assignment->dynamic_checklist_id.'-'.$assignment->sede_id;
            if ($unified->has($key)) {
                continue;
            }

            $matchingManual = $allAssignments->where('dynamic_checklist_id', $assignment->dynamic_checklist_id)->where('sede_id', $assignment->sede_id);
            $matchingCompleted = $completedInspectionsAll->where('dynamic_checklist_id', $assignment->dynamic_checklist_id)->where('sede_id', $assignment->sede_id);
            $completedTotal = $matchingCompleted->count();
            $completedPassed = $matchingCompleted->where('result', 'Pass')->count();

            $unified->put($key, [
                'id' => $assignment->id,
                'type' => 'inspection',
                'title' => $assignment->checklist?->title,
                'sede' => $assignment->sede?->nombre_sede,
                'sede_id' => $assignment->sede_id,
                'empresa' => $assignment->sede?->empresa?->nombre,
                'due_date' => $assignment->due_date,
                'status' => $assignment->status,
                'checklist_id' => $assignment->dynamic_checklist_id,
                'source' => 'manual',
                'registry_code' => $assignment->checklist?->registry_code ?? ('TX-'.$assignment->id),
                'checklist_count' => $matchingManual->count(),
                'completed_count' => $completedTotal,
                'passed_count' => $completedPassed,
                'compliance_pct' => $completedTotal > 0 ? round(($completedPassed / $completedTotal) * 100) : null,
            ]);
        }

        return $unified->values();
    }

    public function buildUnifiedTrainings(Collection $contractTrainings, Collection $allTrainings): Collection
    {
        $unified = collect();

        foreach ($contractTrainings as $plan) {
            $matchingTrainings = $allTrainings->where('sede_id', $plan->sede_id)
                ->filter(fn ($t) => ($plan->checklist_id && $t->dynamic_checklist_id == $plan->checklist_id)
                    || (Str::slug($t->title) === Str::slug($plan->activity_name)));

            $key = $plan->checklist_id ? ('c_'.$plan->checklist_id.'_'.$plan->sede_id) : ('t_'.Str::slug($plan->activity_name).'_'.$plan->sede_id);

            $unified->put($key, [
                'id' => $plan->id,
                'type' => 'training',
                'title' => $plan->checklist?->title ?? $plan->activity_name,
                'sede' => $plan->sede?->nombre_sede,
                'sede_id' => $plan->sede_id,
                'empresa' => $plan->sede?->empresa?->nombre,
                'due_date' => Carbon::now()->endOfMonth(),
                'status' => $this->resolveGroupStatus($matchingTrainings),
                'checklist_id' => $plan->checklist_id,
                'source' => 'contract',
                'registry_code' => $plan->checklist?->registry_code ?? $plan->activity_code ?? ('AN-'.$plan->id),
            ]);
        }

        foreach ($allTrainings as $t) {
            $key = $t->dynamic_checklist_id ? ('c_'.$t->dynamic_checklist_id.'_'.$t->sede_id) : ('t_'.Str::slug($t->title).'_'.$t->sede_id);
            if ($unified->has($key)) {
                continue;
            }

            $unified->put($key, [
                'id' => $t->id,
                'type' => 'training',
                'title' => $t->title,
                'sede' => $t->sede?->nombre_sede,
                'sede_id' => $t->sede_id,
                'empresa' => $t->sede?->empresa?->nombre,
                'due_date' => $t->date,
                'status' => $t->status,
                'checklist_id' => $t->dynamic_checklist_id,
                'source' => 'manual',
                'registry_code' => $t->dynamicChecklist?->registry_code ?? ('EDU-'.$t->id),
            ]);
        }

        return $unified->values();
    }

    public function buildUnifiedReports(Collection $contractReports, Collection $allReports, string $queryMonth, int $year, int $selectedMonth): Collection
    {
        $unified = collect();

        foreach ($contractReports as $plan) {
            $matchingReport = MasterReport::where('sede_id', $plan->sede_id)->where('periodo', $queryMonth)->orderByDesc('created_at')->first()
                ?? MasterReport::where('sede_id', $plan->sede_id)->whereYear('created_at', $year)->whereMonth('created_at', $selectedMonth)->orderByDesc('created_at')->first();

            $accion = $this->resolveReportAction($matchingReport, 'ejecutar');
            $key = 'r_'.Str::slug($plan->activity_name).'_'.$plan->sede_id;

            $unified->put($key, [
                'id' => $plan->id,
                'type' => 'report',
                'title' => $plan->checklist?->title ?? $plan->activity_name,
                'sede' => $plan->sede?->nombre_sede,
                'sede_id' => $plan->sede_id,
                'empresa' => $plan->sede?->empresa?->nombre,
                'due_date' => Carbon::now()->endOfMonth(),
                'status' => $this->mapReportActionToStatus($accion),
                'checklist_id' => null,
                'source' => 'contract',
                'registry_code' => $plan->checklist?->registry_code ?? $plan->activity_code ?? ('AN-'.$plan->id),
                'accion' => $accion,
                'master_report_id' => $matchingReport?->id,
                'observacion_revision' => $matchingReport?->observacion_revision,
            ]);
        }

        $usedMasterReportIds = $unified->pluck('master_report_id')->filter()->values()->toArray();

        foreach ($allReports as $r) {
            if (in_array($r->id, $usedMasterReportIds)) {
                continue;
            }
            $key = 'r_'.Str::slug($r->titulo).'_'.$r->sede_id;
            if ($unified->has($key)) {
                continue;
            }

            $accionAdhoc = $this->resolveReportAction($r, 'reanudar');
            $unified->put($key, [
                'id' => $r->id,
                'type' => 'report',
                'title' => $r->titulo,
                'sede' => $r->sede?->nombre_sede,
                'sede_id' => $r->sede_id,
                'empresa' => $r->sede?->empresa?->nombre,
                'due_date' => $r->created_at->addDays(7),
                'status' => $this->mapReportActionToStatus($accionAdhoc),
                'source' => 'manual',
                'registry_code' => 'MET-'.$r->id,
                'accion' => $accionAdhoc,
                'master_report_id' => $r->id,
                'observacion_revision' => $r->observacion_revision,
            ]);
        }

        return $unified->values();
    }

    private function resolveGroupStatus(Collection $items): string
    {
        if ($items->isEmpty()) {
            return 'scheduled';
        }
        if ($items->whereIn('status', ['completed', 'finalizado'])->isNotEmpty()) {
            return 'completed';
        }

        return $items->where('status', 'in_progress')->isNotEmpty() ? 'in_progress' : 'pending';
    }

    private function resolveReportAction(?MasterReport $report, string $fallback): string
    {
        return match (true) {
            is_null($report) => $fallback,
            $report->estado === 'aprobado' => 'aprobado',
            $report->estado === 'finalizado' => 'pendiente',
            $report->estado === 'en_redaccion' && ! is_null($report->observacion_revision) => 'corregir',
            $report->estado === 'en_redaccion' => 'reanudar',
            default => $fallback,
        };
    }

    private function mapReportActionToStatus(string $accion): string
    {
        return match ($accion) {
            'aprobado' => 'completed',
            'pendiente' => 'pending_review',
            'corregir' => 'correction',
            'reanudar' => 'in_progress',
            default => 'scheduled',
        };
    }
}
