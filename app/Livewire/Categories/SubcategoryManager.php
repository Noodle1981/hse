<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use App\Models\Subcategory;
use Livewire\Component;
use Illuminate\Validation\Rule;

class SubcategoryManager extends Component
{
    public Category $category;
    public $subcategories = [];
    
    // Formulario para nueva subcategoría
    public string $newCode = '';
    public string $newName = '';
    public string $newDescription = '';
    public bool $showForm = false;
    
    // Edición
    public ?int $editingId = null;
    public string $editCode = '';
    public string $editName = '';
    public string $editDescription = '';

    public function mount(Category $category)
    {
        $this->category = $category;
        $this->loadSubcategories();
    }

    public function loadSubcategories()
    {
        $this->subcategories = $this->category->subcategories()->orderBy('order')->get();
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate([
            'newCode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('subcategories', 'code')
            ],
            'newName' => 'required|string|max:255',
            'newDescription' => 'nullable|string',
        ]);

        Subcategory::create([
            'category_id' => $this->category->id,
            'code' => $this->newCode,
            'name' => $this->newName,
            'description' => $this->newDescription ?: null,
            'order' => $this->subcategories->count()
        ]);

        $this->loadSubcategories();
        $this->resetForm();
        $this->showForm = false;
        session()->flash('message', 'Subcategoría creada exitosamente');
    }

    public function edit(int $id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $this->editingId = $id;
        $this->editCode = $subcategory->code;
        $this->editName = $subcategory->name;
        $this->editDescription = $subcategory->description ?? '';
    }

    public function update()
    {
        $this->validate([
            'editCode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('subcategories', 'code')->ignore($this->editingId)
            ],
            'editName' => 'required|string|max:255',
            'editDescription' => 'nullable|string',
        ]);

        $subcategory = Subcategory::findOrFail($this->editingId);
        $subcategory->update([
            'code' => $this->editCode,
            'name' => $this->editName,
            'description' => $this->editDescription ?: null,
        ]);

        $this->loadSubcategories();
        $this->cancelEdit();
        session()->flash('message', 'Subcategoría actualizada exitosamente');
    }

    public function delete(int $id)
    {
        $subcategory = Subcategory::findOrFail($id);
        
        if ($subcategory->checklists()->count() > 0) {
            session()->flash('error', 'No se puede eliminar una subcategoría con checklists asociados');
            return;
        }

        $subcategory->delete();
        $this->loadSubcategories();
        session()->flash('message', 'Subcategoría eliminada exitosamente');
    }

    public function cancelEdit()
    {
        $this->editingId = null;
        $this->editCode = '';
        $this->editName = '';
        $this->editDescription = '';
    }

    private function resetForm()
    {
        $this->newCode = '';
        $this->newName = '';
        $this->newDescription = '';
    }

    public function render()
    {
        return view('livewire.categories.subcategory-manager');
    }
}
