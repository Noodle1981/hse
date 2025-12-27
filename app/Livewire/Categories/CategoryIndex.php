<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showInactive = false;

    public function render()
    {
        $categories = Category::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when(!$this->showInactive, fn($q) => $q->where('is_active', true))
            ->withCount('subcategories')
            ->orderBy('order')
            ->paginate(10);

        return view('livewire.categories.category-index', compact('categories'));
    }

    public function delete(Category $category)
    {
        if ($category->checklists()->count() > 0) {
            session()->flash('error', 'No se puede eliminar una categoría con checklists asociados');
            return;
        }

        $category->delete();
        session()->flash('message', 'Categoría eliminada exitosamente');
    }
}
