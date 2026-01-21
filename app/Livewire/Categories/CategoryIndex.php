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

        return view('livewire.categories.category-index', compact('categories'))
            ->layout('components.layouts.app');
    }

    public function toggleStatus(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);
        
        $status = $category->is_active ? 'activada' : 'desactivada';
        session()->flash('message', "Categoría {$status} exitosamente");
    }
}
