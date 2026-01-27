<?php

namespace App\Livewire\Companies;

use App\Models\Empresa;
use Livewire\Component;
use Livewire\WithPagination;

class CompanyIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showDeleted = false;

    public bool $confirmingDeletion = false;
    public ?int $companyToDeleteId = null;
    public string $companyToDeleteName = '';

    public function updatedShowDeleted()
    {
        $this->resetPage();
    }

    public function render()
    {
        $currentUser = auth()->user();
        $query = Empresa::query();

        // Tenancy
        if (!$currentUser->isSuperAdmin()) {
            $query->where('consultora_id', $currentUser->consultora_id);
        }

        // Soft Deletes Filter
        if ($this->showDeleted) {
            $query->onlyTrashed();
        }

        $companies = $query->when($this->search, fn($q) => $q->where(function($subq) {
                $subq->where('nombre', 'like', "%{$this->search}%")
                     ->orWhere('razon_social', 'like', "%{$this->search}%")
                     ->orWhere('cuit', 'like', "%{$this->search}%");
            }))
            ->withCount('sedes')
            ->orderBy('nombre')
            ->paginate(10);

        return view('livewire.companies.company-index', compact('companies'))
            ->layout('components.layouts.app');
    }

    public function confirmDelete($id, $name)
    {
        $this->companyToDeleteId = $id;
        $this->companyToDeleteName = $name;
        $this->confirmingDeletion = true;
    }

    public function cancelDelete()
    {
        $this->confirmingDeletion = false;
        $this->companyToDeleteId = null;
        $this->companyToDeleteName = '';
    }

    public function delete()
    {
        if (!$this->companyToDeleteId) return;

        $empresa = Empresa::findOrFail($this->companyToDeleteId);
        
        // Soft Delete permite borrar aunque tenga sedes, manteniendo integridad histórica
        $empresa->delete();
        
        $this->cancelDelete();
        session()->flash('message', 'Empresa dada de baja exitosamente');
    }

    public function restore($id)
    {
        $empresa = Empresa::withTrashed()->findOrFail($id);
        $empresa->restore();
        
        session()->flash('message', 'Empresa restaurada exitosamente');
    }
}
