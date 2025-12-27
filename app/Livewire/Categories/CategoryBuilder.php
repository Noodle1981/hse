<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;
use Illuminate\Validation\Rule;

class CategoryBuilder extends Component
{
    public ?Category $category = null;
    public string $code = '';
    public string $name = '';
    public string $description = '';
    public string $color = '#6B7280';
    public string $icon = '';
    public bool $isActive = true;
    public int $order = 0;

    protected function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('categories', 'code')->ignore($this->category->id ?? null)
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
            'isActive' => 'boolean',
            'order' => 'integer|min:0'
        ];
    }

    public function mount(?int $id = null)
    {
        if ($id) {
            $this->category = Category::findOrFail($id);
            $this->code = $this->category->code;
            $this->name = $this->category->name;
            $this->description = $this->category->description ?? '';
            $this->color = $this->category->color;
            $this->icon = $this->category->icon ?? '';
            $this->isActive = $this->category->is_active;
            $this->order = $this->category->order;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description ?: null,
            'color' => $this->color,
            'icon' => $this->icon ?: null,
            'is_active' => $this->isActive,
            'order' => $this->order
        ];

        if ($this->category) {
            $this->category->update($data);
            $message = 'Categoría actualizada exitosamente';
        } else {
            Category::create($data);
            $message = 'Categoría creada exitosamente';
        }

        session()->flash('message', $message);
        return $this->redirect(route('categories.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.categories.category-builder');
    }
}
