<?php

namespace App\Services;

use App\Models\DynamicInspection;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\User;
use App\Models\VisitCertificate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class InspectionHistoryService
{
    public function getGestorHistory(User $user, Request $request): array
    {
        $query = DynamicInspection::completed();
        if (! $user->isSuperAdmin() && $user->consultora_id) {
            $query->whereHas('sede.empresa', fn ($q) => $q->where('consultora_id', $user->consultora_id));
        }

        $this->applyCommonHistoryFilters($query, $request);
        if ($request->tecnico_id) {
            $query->where('user_id', $request->tecnico_id);
        }

        $allGroups = $query->select(DB::raw('DATE(completed_at) as completed_date'), 'sede_id', 'user_id')
            ->groupBy(DB::raw('DATE(completed_at)'), 'sede_id', 'user_id')
            ->orderBy(DB::raw('DATE(completed_at)'), 'desc')
            ->orderBy('sede_id', 'desc')
            ->orderBy('user_id', 'desc')
            ->get();

        $paginatedGroups = $this->paginateGroups($allGroups, $request, function ($group) {
            $sede = Sede::with('empresa')->find($group->sede_id);
            $techUser = User::find($group->user_id);

            $visitCertificate = VisitCertificate::where('sede_id', $group->sede_id)
                ->where('user_id', $group->user_id)
                ->whereDate('visit_date', $group->completed_date)
                ->first();

            $inspections = DynamicInspection::with('checklist')
                ->where('user_id', $group->user_id)
                ->where('sede_id', $group->sede_id)
                ->whereDate('completed_at', $group->completed_date)
                ->completed()
                ->latest('completed_at')
                ->get();

            return $this->formatHistoryGroup($group, $sede, $visitCertificate, $inspections, [
                'tecnico_id' => $group->user_id,
                'tecnico_nombre' => $techUser ? $techUser->name : 'Técnico Desconocido',
            ]);
        });

        $tecnicos = User::where(fn ($q) => $q->where('rol', 'tecnico')->orWhere('rol', 'Técnico'))
            ->when($user->consultora_id, fn ($q) => $q->where('consultora_id', $user->consultora_id))
            ->orderBy('name')
            ->get();

        return [
            'inspections' => $paginatedGroups,
            'empresas' => $this->getScopedEmpresas($user),
            'tecnicos' => $tecnicos,
            'filters' => $request->only(['empresa', 'sede', 'fecha_inicio', 'fecha_fin', 'tecnico_id']),
        ];
    }

    public function getTecnicoHistory(User $user, Request $request): array
    {
        $query = DynamicInspection::where('user_id', $user->id)->completed();
        $this->applyCommonHistoryFilters($query, $request);
        if ($request->checklist_id) {
            $query->where('dynamic_checklist_id', $request->checklist_id);
        }

        $allGroups = $query->select(DB::raw('DATE(completed_at) as completed_date'), 'sede_id')
            ->groupBy(DB::raw('DATE(completed_at)'), 'sede_id')
            ->orderBy(DB::raw('DATE(completed_at)'), 'desc')
            ->orderBy('sede_id', 'desc')
            ->get();

        $paginatedGroups = $this->paginateGroups($allGroups, $request, function ($group) use ($user, $request) {
            $sede = Sede::with('empresa')->find($group->sede_id);
            $visitCertificate = VisitCertificate::where('sede_id', $group->sede_id)
                ->whereDate('visit_date', $group->completed_date)
                ->first();

            $inspections = DynamicInspection::with(['checklist', 'asset'])
                ->where('user_id', $user->id)
                ->where('sede_id', $group->sede_id)
                ->whereDate('completed_at', $group->completed_date)
                ->completed()
                ->when($request->checklist_id, fn ($q) => $q->where('dynamic_checklist_id', $request->checklist_id))
                ->latest('completed_at')
                ->get();

            return $this->formatHistoryGroup($group, $sede, $visitCertificate, $inspections, [], includeAsset: true);
        });

        return [
            'inspections' => $paginatedGroups,
            'empresas' => $this->getScopedEmpresas($user),
            'filters' => $request->only(['empresa', 'sede', 'fecha_inicio', 'fecha_fin', 'checklist_id']),
        ];
    }

    private function applyCommonHistoryFilters($query, Request $request): void
    {
        if ($request->empresa) {
            $query->whereHas('sede', fn ($q) => $q->where('empresa_id', $request->empresa));
        }
        if ($request->sede) {
            $query->where('sede_id', $request->sede);
        }
        if ($request->fecha_inicio) {
            $query->whereDate('completed_at', '>=', $request->fecha_inicio);
        }
        if ($request->fecha_fin) {
            $query->whereDate('completed_at', '<=', $request->fecha_fin);
        }
    }

    private function paginateGroups($allGroups, Request $request, callable $mapper): LengthAwarePaginator
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        $currentPageItems = $allGroups->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $currentPageItems,
            $allGroups->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
        $paginator->appends($request->all());
        $paginator->setCollection($currentPageItems->map($mapper));

        return $paginator;
    }

    private function formatHistoryGroup($group, ?Sede $sede, ?VisitCertificate $visitCertificate, $inspections, array $extra = [], bool $includeAsset = false): array
    {
        $totalCount = $inspections->count();
        $successCount = $inspections->where('result', '!=', 'Fail')->count();
        $failedCount = $inspections->where('result', 'Fail')->count();

        $checklistDetails = $inspections->map(function ($ins) use ($includeAsset) {
            $item = [
                'id' => $ins->id,
                'checklist_title' => $ins->checklist?->title,
                'registry_code' => $ins->checklist?->registry_code,
                'completed_time' => $ins->completed_at ? $ins->completed_at->format('H:i') : '00:00',
                'supervisor_name' => $ins->supervisor_name ?: 'Sin Firmar',
                'result' => $ins->result,
                'has_failed' => $ins->result === 'Fail',
            ];

            if ($includeAsset) {
                $item['asset'] = $ins->asset ? $ins->asset->only(['id', 'name', 'identifier', 'status']) : null;
            }

            return $item;
        });

        return array_merge([
            'completed_date' => $group->completed_date,
            'completed_date_formatted' => Carbon::parse($group->completed_date)->format('d/m/Y'),
            'sede_id' => $group->sede_id,
            'sede_nombre' => $sede ? $sede->nombre_sede : 'Sede Desconocida',
            'empresa_nombre' => $sede?->empresa?->nombre ?? 'Empresa Desconocida',
            'visit_certificate_id' => $visitCertificate?->id,
            'visit_certificate_status' => $visitCertificate?->status,
            'visit_certificate_code' => $visitCertificate?->code,
            'total_count' => $totalCount,
            'success_count' => $successCount,
            'failed_count' => $failedCount,
            'compliance_percentage' => $totalCount > 0 ? round(($successCount / $totalCount) * 100) : 100,
            'checklists' => $checklistDetails,
        ], $extra);
    }

    private function getScopedEmpresas(User $user)
    {
        return Empresa::with(['sedes' => fn ($q) => $q->orderBy('nombre_sede')])
            ->when($user->consultora_id, fn ($q) => $q->where('consultora_id', $user->consultora_id))
            ->orderBy('nombre')
            ->get();
    }
}
