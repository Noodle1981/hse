<?php

namespace App\Http\Controllers;

use App\Models\AssetType;
use App\Models\Category;
use App\Models\DynamicChecklist;
use App\Models\Empresa;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AssetTypeController extends Controller
{
    /**
     * Listado de Tipos de Activos con su esquema de metadata.
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

        $assetTypes = AssetType::query()
            ->with(['category', 'empresa'])
            ->where(function ($q) use ($assignedEmpresaIds) {
                $q->whereIn('empresa_id', $assignedEmpresaIds)
                    ->orWhereNull('empresa_id');
            })
            ->latest()
            ->get();

        // Categorías con sus subcategorías anidadas
        $categories = Category::where('is_active', true)
            ->with(['subcategories' => fn ($q) => $q->where('is_active', true)->orderBy('order')])
            ->orderBy('order')
            ->get(['id', 'name', 'color', 'icon']);

        $empresas = Empresa::whereIn('id', $assignedEmpresaIds)->get(['id', 'nombre']);

        // Todos los checklists activos con sus IDs de categoría y subcategoría para filtrar en frontend
        $checklists = DynamicChecklist::where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title', 'registry_code', 'category_id', 'subcategory_id']);

        return Inertia::render('Gestor/Assets/Types/Index', [
            'assetTypes'  => $assetTypes,
            'categories'  => $categories,
            'empresas'    => $empresas,
            'checklists'  => $checklists,
        ]);
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
            'metadata_schema' => $request->metadata_schema ?? [],
            'photo_schema' => $request->photo_schema ?? [],
        ];

        if ($request->id) {
            $assetType = AssetType::findOrFail($request->id);
            $assetType->update($data);
            $msg = "Tipo de activo '{$assetType->name}' actualizado correctamente.";
        } else {
            $assetType = AssetType::create($data);
            $msg = "Tipo de activo '{$assetType->name}' creado correctamente.";
        }

        return redirect()->route('gestor.asset-types.index')->with('message', $msg);
    }

    /**
     * Importar campos de metadata desde las preguntas de un checklist.
     * Si las preguntas no tienen vincular_a_metadata, genera automáticamente una clave limpia.
     */
    public function importFromChecklist(DynamicChecklist $checklist)
    {
        $questions = $checklist->questions()->get(['id', 'label', 'type', 'vincular_a_metadata']);

        $fields = $questions->map(function ($q) {
            // Mapear tipo de pregunta → tipo de metadata
            $metaType = match ($q->type) {
                'number'           => 'number',
                'date', 'datetime' => 'date',
                default            => 'text',
            };

            // Usar vincular_a_metadata o auto-generar uno a partir del label
            $keyName = ! empty($q->vincular_a_metadata)
                ? $q->vincular_a_metadata
                : $this->generateKeyFromLabel($q->label);

            // Guardar automáticamente el vínculo en la pregunta si no lo tenía
            if (empty($q->vincular_a_metadata)) {
                $q->update(['vincular_a_metadata' => $keyName]);
            }

            // Detectar si es fecha de vencimiento/vigencia para sugerir alerta o dejarla como fecha informativa
            $isExpirationDate = preg_match('/venc|vto|vigenc|proxim|caduc/i', $q->label);
            $isIdentifier = preg_match('/identif|nro|n°|interno|codigo|patente|matricula/i', $q->label);
            $isStatic = preg_match('/tipo|modelo|marca|fabricante|capacidad/i', $q->label);

            $role = 'verificacion'; // default
            $showInCreate = false;

            if ($isIdentifier) {
                $role = 'identificador';
                $showInCreate = true;
            } elseif ($metaType === 'date' && $isExpirationDate) {
                $role = 'dato_con_regla';
            } elseif ($isStatic) {
                $role = 'dato_estatico';
            }

            return [
                'name'            => $keyName,
                'label'           => $q->label,
                'type'            => $metaType,
                'show_in_create'  => $showInCreate,
                'role'            => $role,
                'alert_days'      => ($metaType === 'date' && $isExpirationDate) ? 30 : null,
                'alert_threshold' => null,
                '_imported'       => true,
            ];
        })
        ->filter(fn ($f) => ! empty($f['name']))
        ->unique('name')
        ->values();

        return response()->json(['fields' => $fields]);
    }

    /**
     * Generar clave limpia snake_case a partir de una etiqueta de pregunta.
     */
    private function generateKeyFromLabel(string $label): string
    {
        $clean = preg_replace('/[^\p{L}\p{N}\s]/u', '', $label);
        $slug = \Illuminate\Support\Str::slug($clean, '_');
        $slug = substr($slug, 0, 35);
        return rtrim($slug, '_') ?: 'atributo_' . uniqid();
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
