<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService
{
    public function getPaginatedCategories(Request $request): LengthAwarePaginator
    {
        return Category::query()
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->boolean('showInactive') == false, fn ($q) => $q->where('is_active', true))
            ->withCount('subcategories')
            ->orderBy('order')
            ->paginate(15)
            ->withQueryString();
    }

    public function saveCategory(array $data, $id = null): string
    {
        if ($id) {
            $category = Category::findOrFail($id);
            $category->update($data);

            return 'Categoría actualizada correctamente.';
        }

        Category::create($data);

        return 'Categoría creada correctamente.';
    }

    public function saveSubcategory(Category $category, array $data, $id = null): string
    {
        $data['category_id'] = $category->id;

        if ($id) {
            $subcategory = Subcategory::findOrFail($id);
            $subcategory->update($data);

            return 'Subcategoría actualizada.';
        }

        $data['order'] = $category->subcategories()->count();
        Subcategory::create($data);

        return 'Subcategoría creada.';
    }

    public function toggleModelStatus(string $type, $id): string
    {
        $model = $type === 'category'
            ? Category::findOrFail($id)
            : Subcategory::findOrFail($id);

        $model->update(['is_active' => ! $model->is_active]);
        $status = $model->is_active ? 'activada' : 'desactivada';

        return ucfirst($type)." $status exitosamente.";
    }
}
