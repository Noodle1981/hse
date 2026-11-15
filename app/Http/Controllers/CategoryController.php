<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService) {}

    /**
     * Display a listing of categories.
     */
    public function index(Request $request)
    {
        return Inertia::render('Gestor/Categories/Index', [
            'categories' => $this->categoryService->getPaginatedCategories($request),
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
                Rule::unique('categories', 'name')->ignore($id),
            ],
            'description' => 'nullable|string',
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ]);

        $data = $request->only(['code', 'name', 'description', 'color', 'icon', 'is_active', 'order']);
        $message = $this->categoryService->saveCategory($data, $id);

        if ($request->has('redirect_back')) {
            return back()->with('message', $message);
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
                Rule::unique('subcategories', 'name')->ignore($id),
            ],
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['code', 'name', 'description', 'is_active']);
        $message = $this->categoryService->saveSubcategory($category, $data, $id);

        return back()->with('message', $message);
    }

    /**
     * Toggle status for category or subcategory.
     */
    public function toggleStatus(Request $request, $type, $id)
    {
        $message = $this->categoryService->toggleModelStatus($type, $id);

        return back()->with('message', $message);
    }
}
