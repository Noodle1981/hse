<?php

namespace App\Livewire\Checklists;

use App\Models\DynamicChecklist;
use Livewire\Component;
use Livewire\WithPagination;

class ChecklistIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $categoryFilter = '';

    protected $queryString = ['search', 'categoryFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete(int $id)
    {
        DynamicChecklist::findOrFail($id)->delete();
        session()->flash('message', 'Checklist eliminado correctamente.');
    }

    public function toggleActive(int $id)
    {
        $checklist = DynamicChecklist::findOrFail($id);
        $checklist->update(['is_active' => !$checklist->is_active]);
    }

    public function duplicate(int $id)
    {
        $original = DynamicChecklist::with('questions')->findOrFail($id);
        
        // Crear copia como borrador
        $copy = $original->replicate();
        $copy->title = '📝 [BORRADOR] ' . $original->title . ' (Copia)';
        $copy->is_draft = true;
        $copy->save();
        
        // Copiar todas las preguntas
        foreach ($original->questions as $question) {
            $newQuestion = $question->replicate();
            $newQuestion->dynamic_checklist_id = $copy->id;
            $newQuestion->save();
        }
        
        session()->flash('message', '¡Checklist duplicado! Edita el título y guarda para poder usarlo.');
        return redirect()->route('checklists.edit', $copy);
    }

    public function render()
    {
        $checklists = DynamicChecklist::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category', $this->categoryFilter);
            })
            ->withCount('questions')
            ->latest()
            ->paginate(10);

        return view('livewire.checklists.checklist-index', [
            'checklists' => $checklists,
        ])->layout('layouts.app');
    }
}
