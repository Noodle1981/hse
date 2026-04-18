<?php

namespace App\Livewire\Checklists;

use App\Models\DynamicChecklist;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ChecklistIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $categoryFilter = '';
    public string $statusFilter = 'active'; // 'all', 'active', 'inactive'

    protected $queryString = ['search', 'categoryFilter', 'statusFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleActive(int $id)
    {
        $checklist = DynamicChecklist::findOrFail($id);
        $checklist->update(['is_active' => !$checklist->is_active]);
        
        $status = $checklist->is_active ? 'activado' : 'desactivado';
        session()->flash('message', "Checklist {$status} correctamente.");
    }

    public function duplicate(int $id)
    {
        $original = DynamicChecklist::with(['questions', 'sections'])->findOrFail($id);
        
        // Crear copia como borrador
        $copy = $original->replicate();
        $copy->title = '📝 [BORRADOR] ' . $original->title . ' (Copia)';
        $copy->is_draft = true;
        $copy->save();
        
        // Copiar todas las secciones
        $sectionMapping = [];
        foreach ($original->sections as $section) {
            $newSection = $section->replicate();
            $newSection->dynamic_checklist_id = $copy->id;
            $newSection->save();
            $sectionMapping[$section->id] = $newSection->id;
        }
        
        // Copiar todas las preguntas
        foreach ($original->questions as $question) {
            $newQuestion = $question->replicate();
            $newQuestion->dynamic_checklist_id = $copy->id;
            
            // Actualizar section_id si existe
            if ($question->section_id && isset($sectionMapping[$question->section_id])) {
                $newQuestion->section_id = $sectionMapping[$question->section_id];
            }
            
            $newQuestion->save();
        }
        
        session()->flash('message', '¡Checklist duplicado! Edita el título y guarda para poder usarlo.');
        return redirect()->route('gestor.checklists.edit', $copy);
    }

    public function render()
    {
        $checklists = DynamicChecklist::query()
            ->with(['categoryRelation', 'subcategoryRelation'])
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category_id', $this->categoryFilter);
            })
            ->when($this->statusFilter === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($this->statusFilter === 'inactive', function ($query) {
                $query->where('is_active', false);
            })
            ->withCount('questions')
            ->latest()
            ->paginate(10);

        $categories = \App\Models\Category::active()->orderBy('order')->get();

        return view('livewire.checklists.checklist-index', [
            'checklists' => $checklists,
            'categories' => $categories,
        ]);
    }
}
