<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetType;
use App\Models\Empresa;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
            ->with(['sede.empresa', 'assetType', 'hallazgos' => function ($q) {
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
     * Almacenar uno o varios activos.
     */
    public function store(Request $request)
    {
        // Soporta guardado masivo en lote (varios activos en la misma petición)
        if ($request->has('items') && is_array($request->items)) {
            $request->validate([
                'empresa_id' => 'required|exists:empresas,id',
                'sede_id' => 'required|exists:sedes,id',
                'asset_type_id' => 'required|exists:asset_types,id',
                'items' => 'required|array|min:1',
                'items.*.name' => 'required|string|max:255',
                'items.*.identifier' => 'required|string|max:255|distinct|unique:assets,identifier',
                'items.*.status' => 'required|in:operativo,alerta,fuera_de_servicio',
                'items.*.metadata' => 'nullable|array',
            ], [
                'items.*.identifier.distinct' => 'El identificador :value está repetido dentro del mismo formulario.',
                'items.*.identifier.unique' => 'El identificador :value ya está registrado en el sistema.',
            ]);

            $createdCount = 0;
            $firstAsset = null;

            foreach ($request->items as $item) {
                $asset = Asset::create([
                    'empresa_id' => $request->empresa_id,
                    'sede_id' => $request->sede_id,
                    'asset_type_id' => $request->asset_type_id,
                    'name' => $item['name'],
                    'identifier' => $item['identifier'],
                    'status' => $item['status'] ?? 'operativo',
                    'metadata' => $item['metadata'] ?? [],
                ]);

                if (! $firstAsset) {
                    $firstAsset = $asset;
                }
                $createdCount++;
            }

            $msg = $createdCount === 1
                ? "Activo '{$firstAsset->name}' registrado correctamente."
                : "Se registraron {$createdCount} activos correctamente.";

            return redirect()->route('gestor.assets.index')->with('message', $msg);
        }

        // Guardado tradicional de un solo activo
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'sede_id' => 'required|exists:sedes,id',
            'asset_type_id' => 'required|exists:asset_types,id',
            'name' => 'required|string|max:255',
            'identifier' => 'required|string|max:255|unique:assets,identifier',
            'status' => 'required|in:operativo,alerta,fuera_de_servicio',
            'metadata' => 'nullable|array',
        ], [
            'identifier.unique' => 'El identificador ya está registrado en el sistema.',
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
     * Ficha técnica del activo con expediente fotográfico y timeline.
     */
    public function show(Asset $asset)
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

        // Compilar timeline visual de hallazgos e incidentes
        $evidences = [];

        // 1. Evidencias de inspecciones
        foreach ($asset->dynamicInspections as $insp) {
            if ($insp->result === 'Fail' || ! empty($insp->observaciones)) {
                $evidences[] = [
                    'id' => 'insp_' . $insp->id,
                    'type' => 'inspection',
                    'title' => 'Inspección: ' . ($insp->checklist?->title ?? 'Checklist'),
                    'status' => $insp->result,
                    'date' => $insp->completed_at ? $insp->completed_at->format('d/m/Y H:i') : 'N/D',
                    'user' => $insp->user?->name ?? 'Técnico',
                    'description' => $insp->observaciones ?? 'Inspección realizada.',
                    'photos' => [],
                ];
            }
        }

        // 2. Evidencias de incidentes/siniestros
        foreach ($asset->incidentes as $inc) {
            $incPhotos = $inc->fotos ? $inc->fotos->pluck('foto_url')->toArray() : [];
            $evidences[] = [
                'id' => 'inc_' . $inc->id,
                'type' => 'incident',
                'title' => 'Incidente: ' . ($inc->titulo ?? 'Siniestro registrado'),
                'status' => strtoupper($inc->gravedad ?? 'Incidente'),
                'date' => $inc->fecha_ocurrencia ? $inc->fecha_ocurrencia->format('d/m/Y H:i') : 'N/D',
                'user' => $inc->user?->name ?? 'Usuario',
                'description' => $inc->descripcion ?? 'Reporte de incidente.',
                'photos' => $incPhotos,
            ];
        }

        return Inertia::render('Gestor/Assets/Show', [
            'asset' => $asset,
            'evidences' => $evidences,
        ]);
    }

    /**
     * Cargar/actualizar foto de identificación del activo.
     */
    public function uploadPhoto(Request $request, Asset $asset)
    {
        $request->validate([
            'photo_key' => 'required|string|max:255',
            'photo' => 'required|image|max:15360', // Máx 15MB
        ]);

        $file = $request->file('photo');
        $key = $request->input('photo_key');
        $path = $file->store("assets/{$asset->id}", 'public');

        $photos = $asset->photos ?? [];
        $photos[$key] = Storage::url($path);

        $asset->photos = $photos;
        $asset->save();

        return redirect()->back()->with('message', 'Foto guardada correctamente en la ficha del activo.');
    }

    /**
     * Exportar ficha de activo e historial a formato PDF / Imprimible.
     */
    public function exportPdf(Asset $asset)
    {
        $asset->load(['sede.empresa', 'assetType', 'dynamicInspections.checklist', 'incidentes.fotos']);

        return view('pdf.asset-report', [
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
