<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Services\AssetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AssetController extends Controller
{
    public function __construct(private readonly AssetService $assetService) {}

    /**
     * Listado de activos con KPIs y filtros.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($request->wantsJson() || $request->query('json')) {
            return response()->json(
                $this->assetService->buildFilteredQuery($user, $request)->latest('created_at')->get()
            );
        }

        return Inertia::render('Gestor/Assets/Index', $this->assetService->getIndexData($user, $request));
    }

    /**
     * Formulario de creación de activo.
     */
    public function create(Request $request)
    {
        return Inertia::render('Gestor/Assets/Create', array_merge(
            $this->assetService->getFormOptions(Auth::user()),
            [
                'prefilled' => [
                    'empresa_id' => $request->query('empresa_id', ''),
                    'sede_id' => $request->query('sede_id', ''),
                    'asset_type_id' => $request->query('asset_type_id', ''),
                ],
            ]
        ));
    }

    /**
     * Almacenar uno o varios activos.
     */
    public function store(Request $request)
    {
        if ($request->has('items') && is_array($request->items)) {
            $validated = $request->validate([
                'empresa_id' => 'required|exists:empresas,id',
                'sede_id' => 'required|exists:sedes,id',
                'asset_type_id' => 'required|exists:asset_types,id',
                'items' => 'required|array|min:1',
                'items.*.name' => 'required|string|max:255',
                'items.*.identifier' => 'required|string|max:255|distinct|unique:assets,identifier',
                'items.*.status' => 'required|in:operativo,alerta,fuera_de_servicio',
                'items.*.grupo' => 'nullable|string|max:255',
                'items.*.subgrupo' => 'nullable|string|max:255',
                'items.*.metadata' => 'nullable|array',
            ], [
                'items.*.identifier.distinct' => 'El identificador está repetido dentro del mismo formulario.',
                'items.*.identifier.unique' => 'El identificador ya está registrado en el sistema.',
            ]);

            $msg = $this->assetService->storeBatch($validated);

            return redirect()->route('gestor.assets.index')->with('message', $msg);
        }

        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'sede_id' => 'required|exists:sedes,id',
            'asset_type_id' => 'required|exists:asset_types,id',
            'name' => 'required|string|max:255',
            'identifier' => 'required|string|max:255|unique:assets,identifier',
            'status' => 'required|in:operativo,alerta,fuera_de_servicio',
            'grupo' => 'nullable|string|max:255',
            'subgrupo' => 'nullable|string|max:255',
            'metadata' => 'nullable|array',
        ], [
            'identifier.unique' => 'El identificador ya está registrado en el sistema.',
        ]);

        $asset = $this->assetService->storeSingle($validated);

        return redirect()->route('gestor.assets.show', $asset->id)
            ->with('message', "Activo '{$asset->name}' registrado correctamente.");
    }

    /**
     * Ficha técnica del activo con expediente fotográfico y timeline.
     */
    public function show(Asset $asset)
    {
        return Inertia::render('Gestor/Assets/Show', $this->assetService->getShowData($asset));
    }

    /**
     * Cargar/actualizar foto de identificación del activo.
     */
    public function uploadPhoto(Request $request, Asset $asset)
    {
        $request->validate([
            'photo_key' => 'required|string|max:255',
            'photo' => 'required|image|max:15360',
        ]);

        $this->assetService->uploadPhoto($asset, $request->input('photo_key'), $request->file('photo'));

        return redirect()->back()->with('message', 'Foto guardada correctamente en la ficha del activo.');
    }

    /**
     * Exportar ficha de activo e historial a formato PDF / Imprimible.
     */
    public function exportPdf(Asset $asset)
    {
        $asset->load(['sede.empresa', 'assetType', 'dynamicInspections.checklist', 'incidentes.fotos']);

        return view('pdf.asset-report', ['asset' => $asset]);
    }

    /**
     * Formulario de edición.
     */
    public function edit(Asset $asset)
    {
        $asset->load(['assetType', 'sede.empresa']);

        return Inertia::render('Gestor/Assets/Edit', array_merge(
            ['asset' => $asset],
            $this->assetService->getFormOptions(Auth::user())
        ));
    }

    /**
     * Actualizar activo.
     */
    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'sede_id' => 'required|exists:sedes,id',
            'asset_type_id' => 'required|exists:asset_types,id',
            'name' => 'required|string|max:255',
            'identifier' => 'required|string|max:255|unique:assets,identifier,'.$asset->id,
            'status' => 'required|in:operativo,alerta,fuera_de_servicio',
            'grupo' => 'nullable|string|max:255',
            'subgrupo' => 'nullable|string|max:255',
            'metadata' => 'nullable|array',
        ], [
            'identifier.unique' => 'El identificador ya está registrado en el sistema.',
        ]);

        $this->assetService->updateAsset($asset, $validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Activo '{$asset->name}' actualizado correctamente.",
                'asset' => $asset,
            ]);
        }

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
