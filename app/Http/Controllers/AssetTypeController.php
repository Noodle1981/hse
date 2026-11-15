<?php

namespace App\Http\Controllers;

use App\Models\AssetType;
use App\Models\DynamicChecklist;
use App\Models\User;
use App\Services\AssetTypeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AssetTypeController extends Controller
{
    public function __construct(protected AssetTypeService $assetTypeService) {}

    /**
     * Listado de Tipos de Activos con su esquema de metadata.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        return Inertia::render('Gestor/Assets/Types/Index', $this->assetTypeService->getIndexData($user));
    }

    /**
     * Crear o actualizar un Tipo de Activo y su metadata_schema.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id' => 'nullable|exists:asset_types,id',
            'name' => 'required|string|max:255',
            'empresa_id' => 'nullable|exists:empresas,id',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'group_label' => 'nullable|string|max:255',
            'subgroup_label' => 'nullable|string|max:255',
            'metadata_schema' => 'nullable|array',
            'metadata_schema.*.name' => 'required|string|max:255',
            'metadata_schema.*.label' => 'required|string|max:255',
            'metadata_schema.*.type' => 'required|in:text,number,date',
            'metadata_schema.*.show_in_create' => 'nullable|boolean',
            'metadata_schema.*.role' => 'nullable|string|in:identificador,dato_estatico,dato_con_regla,verificacion',
            'metadata_schema.*.alert_days' => 'nullable|integer|min:0',
            'metadata_schema.*.alert_threshold' => 'nullable|numeric',
            'metadata_schema.*.alert_interval' => 'nullable|numeric|min:0',
            'metadata_schema.*.alert_message' => 'nullable|string|max:500',
            'photo_schema' => 'nullable|array',
            'photo_schema.*.key' => 'required|string|max:255',
            'photo_schema.*.label' => 'required|string|max:255',
            'photo_schema.*.required' => 'nullable|boolean',
        ]);

        $data = [
            'name' => $request->name,
            'empresa_id' => $request->empresa_id,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'group_label' => $request->group_label,
            'subgroup_label' => $request->subgroup_label,
            'metadata_schema' => $request->metadata_schema ?? [],
            'photo_schema' => $request->photo_schema ?? [],
        ];

        $msg = $this->assetTypeService->saveAssetType($data, $request->id);

        return redirect()->route('gestor.asset-types.index')->with('message', $msg);
    }

    /**
     * Importar campos de metadata desde las preguntas de un checklist.
     */
    public function importFromChecklist(DynamicChecklist $checklist)
    {
        return response()->json([
            'fields' => $this->assetTypeService->importFieldsFromChecklist($checklist),
        ]);
    }

    /**
     * Eliminar un Tipo de Activo.
     */
    public function destroy(AssetType $assetType)
    {
        $name = $assetType->name;
        $assetType->delete();

        return redirect()->route('gestor.asset-types.index')
            ->with('message', "Tipo de activo '{$name}' eliminado.");
    }
}
