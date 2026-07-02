<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetType;
use App\Models\Empresa;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AssetController extends Controller
{
    /**
     * Listado de activos con KPIs y filtros.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $empresaQuery = Empresa::query();
        if (! $user->isSuperAdmin()) {
            $empresaQuery->where('consultora_id', $user->consultora_id);
        }
        $assignedEmpresaIds = $empresaQuery->pluck('id')->toArray();

        $query = Asset::query()
            ->with(['sede.empresa', 'assetType'])
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

        // KPIs
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

        $assets = $query->latest('created_at')->paginate(15)->withQueryString();

        $empresas = Empresa::whereIn('id', $assignedEmpresaIds)->get(['id', 'nombre']);
        $sedes = Sede::whereIn('empresa_id', $assignedEmpresaIds)->get(['id', 'nombre_sede', 'empresa_id']);
        $assetTypes = AssetType::whereIn('empresa_id', $assignedEmpresaIds)
            ->orWhereNull('empresa_id')
            ->get(['id', 'name']);

        return Inertia::render('Gestor/Assets/Index', [
            'assets' => $assets,
            'kpis' => $kpis,
            'filters' => $request->only(['search', 'empresa_id', 'sede_id', 'asset_type_id', 'status']),
            'empresas' => $empresas,
            'sedes' => $sedes,
            'assetTypes' => $assetTypes,
        ]);
    }

    /**
     * Formulario de creación de activo.
     */
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $empresaQuery = Empresa::query();
        if (! $user->isSuperAdmin()) {
            $empresaQuery->where('consultora_id', $user->consultora_id);
        }
        $assignedEmpresaIds = $empresaQuery->pluck('id')->toArray();

        $empresas = Empresa::whereIn('id', $assignedEmpresaIds)->get(['id', 'nombre']);
        $sedes = Sede::whereIn('empresa_id', $assignedEmpresaIds)->get(['id', 'nombre_sede', 'empresa_id']);
        $assetTypes = AssetType::whereIn('empresa_id', $assignedEmpresaIds)
            ->orWhereNull('empresa_id')
            ->get();

        return Inertia::render('Gestor/Assets/Create', [
            'empresas' => $empresas,
            'sedes' => $sedes,
            'assetTypes' => $assetTypes,
        ]);
    }

    /**
     * Almacenar un nuevo activo.
     */
    public function store(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'sede_id' => 'required|exists:sedes,id',
            'asset_type_id' => 'required|exists:asset_types,id',
            'name' => 'required|string|max:255',
            'identifier' => 'required|string|max:255',
            'status' => 'required|in:operativo,alerta,fuera_de_servicio',
            'metadata' => 'nullable|array',
        ]);

        $asset = Asset::create([
            'empresa_id' => $request->empresa_id,
            'sede_id' => $request->sede_id,
            'asset_type_id' => $request->asset_type_id,
            'name' => $request->name,
            'identifier' => $request->identifier,
            'status' => $request->status,
            'metadata' => $request->metadata ?? [],
        ]);

        return redirect()->route('gestor.assets.show', $asset->id)
            ->with('message', "Activo '{$asset->name}' registrado correctamente.");
    }

    /**
     * Ficha técnica del activo.
     */
    public function show(Asset $asset)
    {
        $asset->load([
            'sede.empresa',
            'assetType',
            'dynamicInspections' => function ($q) {
                $q->with(['checklist', 'user'])->latest('completed_at')->take(10);
            },
        ]);

        return Inertia::render('Gestor/Assets/Show', [
            'asset' => $asset,
        ]);
    }

    /**
     * Formulario de edición.
     */
    public function edit(Asset $asset)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $empresaQuery = Empresa::query();
        if (! $user->isSuperAdmin()) {
            $empresaQuery->where('consultora_id', $user->consultora_id);
        }
        $assignedEmpresaIds = $empresaQuery->pluck('id')->toArray();

        $empresas = Empresa::whereIn('id', $assignedEmpresaIds)->get(['id', 'nombre']);
        $sedes = Sede::whereIn('empresa_id', $assignedEmpresaIds)->get(['id', 'nombre_sede', 'empresa_id']);
        $assetTypes = AssetType::whereIn('empresa_id', $assignedEmpresaIds)
            ->orWhereNull('empresa_id')
            ->get();

        $asset->load(['assetType', 'sede.empresa']);

        return Inertia::render('Gestor/Assets/Edit', [
            'asset' => $asset,
            'empresas' => $empresas,
            'sedes' => $sedes,
            'assetTypes' => $assetTypes,
        ]);
    }

    /**
     * Actualizar activo.
     */
    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'sede_id' => 'required|exists:sedes,id',
            'asset_type_id' => 'required|exists:asset_types,id',
            'name' => 'required|string|max:255',
            'identifier' => 'required|string|max:255',
            'status' => 'required|in:operativo,alerta,fuera_de_servicio',
            'metadata' => 'nullable|array',
        ]);

        $asset->update([
            'empresa_id' => $request->empresa_id,
            'sede_id' => $request->sede_id,
            'asset_type_id' => $request->asset_type_id,
            'name' => $request->name,
            'identifier' => $request->identifier,
            'status' => $request->status,
            'metadata' => $request->metadata ?? [],
        ]);

        return redirect()->route('gestor.assets.show', $asset->id)
            ->with('message', "Activo '{$asset->name}' actualizado correctamente.");
    }

    /**
     * Eliminar activo (SoftDelete).
     */
    public function destroy(Asset $asset)
    {
        $name = $asset->name;
        $asset->delete();

        return redirect()->route('gestor.assets.index')
            ->with('message', "Activo '{$name}' eliminado de forma segura.");
    }
}
