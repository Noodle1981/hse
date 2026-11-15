<?php

namespace App\Services;

use App\Models\AssetType;
use App\Models\Category;
use App\Models\DynamicChecklist;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AssetTypeService
{
    public function getIndexData(User $user): array
    {
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

        $categories = Category::where('is_active', true)
            ->with(['subcategories' => fn ($q) => $q->where('is_active', true)->orderBy('order')])
            ->orderBy('order')
            ->get(['id', 'name', 'color', 'icon']);

        $empresas = Empresa::whereIn('id', $assignedEmpresaIds)->get(['id', 'nombre']);

        $checklists = DynamicChecklist::where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title', 'registry_code', 'category_id', 'subcategory_id']);

        return [
            'assetTypes' => $assetTypes,
            'categories' => $categories,
            'empresas' => $empresas,
            'checklists' => $checklists,
        ];
    }

    public function saveAssetType(array $data, $id = null): string
    {
        if ($id) {
            $assetType = AssetType::findOrFail($id);
            $assetType->update($data);

            return "Tipo de activo '{$assetType->name}' actualizado correctamente.";
        }

        $assetType = AssetType::create($data);

        return "Tipo de activo '{$assetType->name}' creado correctamente.";
    }

    public function importFieldsFromChecklist(DynamicChecklist $checklist): Collection
    {
        $questions = $checklist->questions()->get(['id', 'label', 'type', 'vincular_a_metadata']);

        return $questions->map(function ($q) {
            $metaType = match ($q->type) {
                'number' => 'number',
                'date', 'datetime' => 'date',
                default => 'text',
            };

            $keyName = ! empty($q->vincular_a_metadata)
                ? $q->vincular_a_metadata
                : $this->generateKeyFromLabel($q->label);

            if (empty($q->vincular_a_metadata)) {
                $q->update(['vincular_a_metadata' => $keyName]);
            }

            $isExpirationDate = preg_match('/venc|vto|vigenc|proxim|caduc/i', $q->label);
            $isIdentifier = preg_match('/identif|nro|n°|interno|codigo|patente|matricula/i', $q->label);
            $isStatic = preg_match('/tipo|modelo|marca|fabricante|capacidad/i', $q->label);

            $role = 'verificacion';
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
                'name' => $keyName,
                'label' => $q->label,
                'type' => $metaType,
                'show_in_create' => $showInCreate,
                'role' => $role,
                'alert_days' => ($metaType === 'date' && $isExpirationDate) ? 30 : null,
                'alert_threshold' => null,
                '_imported' => true,
            ];
        })
            ->filter(fn ($f) => ! empty($f['name']))
            ->unique('name')
            ->values();
    }

    private function generateKeyFromLabel(string $label): string
    {
        $clean = preg_replace('/[^\p{L}\p{N}\s]/u', '', $label);
        $slug = substr(Str::slug($clean, '_'), 0, 35);

        return rtrim($slug, '_') ?: 'atributo_'.uniqid();
    }
}
