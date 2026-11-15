<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetMetadataLog;
use App\Models\AssetType;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AssetService
{
    /**
     * Get empresa IDs accessible to the user.
     */
    public function getUserEmpresaIds(User $user): array
    {
        return Empresa::query()
            ->when(! $user->isSuperAdmin(), fn ($q) => $q->where('consultora_id', $user->consultora_id))
            ->pluck('id')
            ->toArray();
    }

    /**
     * Build filtered Asset query for index and JSON endpoints.
     */
    public function buildFilteredQuery(User $user, Request $request): Builder
    {
        $assignedEmpresaIds = $this->getUserEmpresaIds($user);

        $query = Asset::query()
            ->with(['sede.empresa', 'assetType.category.checklists', 'hallazgos' => function ($q) {
                $q->whereIn('estado', ['pendiente', 'en_proceso']);
            }])
            ->whereIn('empresa_id', $assignedEmpresaIds);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('identifier', 'like', "%{$request->search}%");
            });
        }

        if ($request->empresa_id) {
            $query->where('empresa_id', $request->empresa_id);
        }

        if ($request->sede_id) {
            $query->where('sede_id', $request->sede_id);
        }

        if ($request->asset_type_id) {
            $query->where('asset_type_id', $request->asset_type_id);
        }

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->grupo) {
            $query->where('grupo', $request->grupo);
        }

        if ($request->subgrupo) {
            $query->where('subgrupo', $request->subgrupo);
        }

        return $query;
    }

    /**
     * Build Inertia props for the Asset index page.
     */
    public function getIndexData(User $user, Request $request): array
    {
        $assignedEmpresaIds = $this->getUserEmpresaIds($user);
        $query = $this->buildFilteredQuery($user, $request);

        $statsBase = Asset::query()->whereIn('empresa_id', $assignedEmpresaIds);
        if ($request->sede_id) {
            $statsBase->where('sede_id', $request->sede_id);
        }
        if ($request->empresa_id) {
            $statsBase->where('empresa_id', $request->empresa_id);
        }

        $kpis = [
            'total' => (clone $statsBase)->count(),
            'operativos' => (clone $statsBase)->where('status', 'operativo')->count(),
            'alerta' => (clone $statsBase)->where('status', 'alerta')->count(),
            'fuera_de_servicio' => (clone $statsBase)->where('status', 'fuera_de_servicio')->count(),
        ];

        $viewMode = $request->input('view_mode', 'grouped');
        if ($viewMode === 'grouped') {
            $assetsData = $query->latest('created_at')->get();
            $assets = [
                'data' => $assetsData,
                'total' => $assetsData->count(),
                'from' => 1,
                'to' => $assetsData->count(),
                'links' => [],
            ];
        } else {
            $assets = $query->latest('created_at')->paginate(15)->withQueryString();
        }

        return [
            'assets' => $assets,
            'kpis' => $kpis,
            'filters' => $request->only(['search', 'empresa_id', 'sede_id', 'asset_type_id', 'status', 'view_mode']),
            'empresas' => Empresa::whereIn('id', $assignedEmpresaIds)->get(['id', 'nombre']),
            'sedes' => Sede::whereIn('empresa_id', $assignedEmpresaIds)->get(['id', 'nombre_sede', 'empresa_id']),
            'assetTypes' => AssetType::whereIn('empresa_id', $assignedEmpresaIds)
                ->orWhereNull('empresa_id')
                ->get(['id', 'name', 'category_id']),
        ];
    }

    /**
     * Get empresas, sedes, and assetTypes for create/edit forms.
     */
    public function getFormOptions(User $user): array
    {
        $assignedEmpresaIds = $this->getUserEmpresaIds($user);

        return [
            'empresas' => Empresa::whereIn('id', $assignedEmpresaIds)->get(['id', 'nombre']),
            'sedes' => Sede::whereIn('empresa_id', $assignedEmpresaIds)->get(['id', 'nombre_sede', 'empresa_id']),
            'assetTypes' => AssetType::whereIn('empresa_id', $assignedEmpresaIds)
                ->orWhereNull('empresa_id')
                ->get(),
        ];
    }

    /**
     * Create multiple assets in a batch and return summary message.
     */
    public function storeBatch(array $validated): string
    {
        $createdCount = 0;
        $firstAsset = null;

        foreach ($validated['items'] as $item) {
            $asset = Asset::create([
                'empresa_id' => $validated['empresa_id'],
                'sede_id' => $validated['sede_id'],
                'asset_type_id' => $validated['asset_type_id'],
                'name' => $item['name'],
                'identifier' => $item['identifier'],
                'status' => $item['status'] ?? 'operativo',
                'grupo' => $item['grupo'] ?? null,
                'subgrupo' => $item['subgrupo'] ?? null,
                'metadata' => $item['metadata'] ?? [],
            ]);

            if (! $firstAsset) {
                $firstAsset = $asset;
            }
            $createdCount++;
        }

        return $createdCount === 1
            ? "Activo '{$firstAsset->name}' registrado correctamente."
            : "Se registraron {$createdCount} activos correctamente.";
    }

    /**
     * Create a single asset.
     */
    public function storeSingle(array $validated): Asset
    {
        return Asset::create([
            'empresa_id' => $validated['empresa_id'],
            'sede_id' => $validated['sede_id'],
            'asset_type_id' => $validated['asset_type_id'],
            'name' => $validated['name'],
            'identifier' => $validated['identifier'],
            'status' => $validated['status'],
            'grupo' => $validated['grupo'] ?? null,
            'subgrupo' => $validated['subgrupo'] ?? null,
            'metadata' => $validated['metadata'] ?? [],
        ]);
    }

    /**
     * Load asset technical sheet data, timeline evidences, and metadata logs.
     */
    public function getShowData(Asset $asset): array
    {
        $asset->load([
            'sede.empresa',
            'assetType',
            'dynamicInspections' => function ($q) {
                $q->with(['checklist', 'user', 'answers.question'])->latest('completed_at')->take(15);
            },
            'incidentes' => function ($q) {
                $q->with(['user', 'fotos'])->latest('fecha_ocurrencia')->take(10);
            },
            'hallazgos' => function ($q) {
                $q->whereIn('estado', ['pendiente', 'en_proceso']);
            },
        ]);

        $metadataLogs = AssetMetadataLog::where('asset_id', $asset->id)
            ->with(['inspection.checklist', 'changedByUser'])
            ->latest('created_at')
            ->get();

        $evidences = [];
        foreach ($asset->dynamicInspections as $insp) {
            if ($insp->result === 'Fail' || ! empty($insp->observaciones)) {
                $evidences[] = [
                    'id' => 'insp_'.$insp->id,
                    'type' => 'inspection',
                    'title' => 'Inspección: '.($insp->checklist?->title ?? 'Checklist'),
                    'status' => $insp->result,
                    'date' => $insp->completed_at ? $insp->completed_at->format('d/m/Y H:i') : 'N/D',
                    'user' => $insp->user?->name ?? 'Técnico',
                    'description' => $insp->observaciones ?? 'Inspección realizada.',
                    'photos' => [],
                ];
            }
        }

        foreach ($asset->incidentes as $inc) {
            $evidences[] = [
                'id' => 'inc_'.$inc->id,
                'type' => 'incident',
                'title' => 'Incidente: '.($inc->titulo ?? 'Siniestro registrado'),
                'status' => strtoupper($inc->gravedad ?? 'Incidente'),
                'date' => $inc->fecha_ocurrencia ? $inc->fecha_ocurrencia->format('d/m/Y H:i') : 'N/D',
                'user' => $inc->user?->name ?? 'Usuario',
                'description' => $inc->descripcion ?? 'Reporte de incidente.',
                'photos' => $inc->fotos ? $inc->fotos->pluck('foto_url')->toArray() : [],
            ];
        }

        return [
            'asset' => $asset,
            'evidences' => $evidences,
            'metadataLogs' => $metadataLogs,
        ];
    }

    /**
     * Store and attach a photo to the asset's photo map.
     */
    public function uploadPhoto(Asset $asset, string $key, UploadedFile $file): void
    {
        $path = $file->store("assets/{$asset->id}", 'public');
        $photos = $asset->photos ?? [];
        $photos[$key] = Storage::url($path);

        $asset->photos = $photos;
        $asset->save();
    }

    /**
     * Update an existing asset's attributes.
     */
    public function updateAsset(Asset $asset, array $validated): Asset
    {
        $asset->update([
            'empresa_id' => $validated['empresa_id'],
            'sede_id' => $validated['sede_id'],
            'asset_type_id' => $validated['asset_type_id'],
            'name' => $validated['name'],
            'identifier' => $validated['identifier'],
            'status' => $validated['status'],
            'grupo' => $validated['grupo'] ?? null,
            'subgrupo' => $validated['subgrupo'] ?? null,
            'metadata' => $validated['metadata'] ?? [],
        ]);

        return $asset;
    }
}
