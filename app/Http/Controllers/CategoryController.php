<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request)
    {
        $categories = Category::query()
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->boolean('showInactive') == false, fn($q) => $q->where('is_active', true))
            ->withCount('subcategories')
            ->orderBy('order')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Gestor/Categories/Index', [
            'categories' => $categories,
            'filters' => $request->only(['search', 'showInactive']),
        ]);
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return Inertia::render('Gestor/Categories/Builder', [
            'category' => null,
        ]);
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        return Inertia::render('Gestor/Categories/Builder', [
            'category' => $category,
        ]);
    }

    /**
     * Store or update category.
     */
    public function save(Request $request)
    {
        $id = $request->input('id');
        
        $request->validate([
            'code' => 'required|string|max:50',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($id)
            ],
            'description' => 'nullable|string',
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'order' => 'integer|min:0'
        ]);

        $data = $request->only(['code', 'name', 'description', 'color', 'icon', 'is_active', 'order']);

        if ($id) {
            $category = Category::findOrFail($id);
            $category->update($data);
            $message = 'Categoría actualizada correctamente.';
        } else {
            Category::create($data);
            $message = 'Categoría creada correctamente.';
        }

        return redirect()->route('gestor.categories.index')->with('message', $message);
    }

    /**
     * Display subcategories for a specific category.
     */
    public function subcategories(Category $category)
    {
        return Inertia::render('Gestor/Categories/Subcategories', [
            'category' => $category,
            'subcategories' => $category->subcategories()->orderBy('order')->get(),
        ]);
    }

    /**
     * Save/Update subcategory.
     */
    public function saveSubcategory(Request $request, Category $category)
    {
        $id = $request->input('id');
        
        $request->validate([
            'code' => 'required|string|max:50',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subcategories', 'name')->ignore($id)
            ],
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['code', 'name', 'description', 'is_active']);
        $data['category_id'] = $category->id;

        if ($id) {
            $subcategory = Subcategory::findOrFail($id);
            $subcategory->update($data);
            $message = 'Subcategoría actualizada.';
        } else {
            $data['order'] = $category->subcategories()->count();
            Subcategory::create($data);
            $message = 'Subcategoría creada.';
        }

        return back()->with('message', $message);
    }

    /**
     * Toggle status for category or subcategory.
     */
    public function toggleStatus(Request $request, $type, $id)
    {
        if ($type === 'category') {
            $model = Category::findOrFail($id);
        } else {
            $model = Subcategory::findOrFail($id);
        }

        $model->update(['is_active' => !$model->is_active]);
        $status = $model->is_active ? 'activada' : 'desactivada';

        return back()->with('message', ucfirst($type) . " $status exitosamente.");
    }
}
