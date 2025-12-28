<?php

namespace App\Livewire\Companies;

use App\Models\Empresa;
use Livewire\Component;
use Livewire\WithPagination;

class CompanyIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function render()
    {
        $companies = Empresa::query()
            ->when($this->search, fn($q) => $q->where('nombre', 'like', "%{$this->search}%")
                ->orWhere('razon_social', 'like', "%{$this->search}%")
                ->orWhere('cuit', 'like', "%{$this->search}%"))
            ->withCount('sedes')
            ->orderBy('nombre')
            ->paginate(10);

        return view('livewire.companies.company-index', compact('companies'));
    }

    public function delete(Empresa $empresa)
    {
        if ($empresa->sedes()->count() > 0) {
            session()->flash('error', 'No se puede eliminar una empresa con sedes asociadas');
            return;
        }

        $empresa->delete();
        session()->flash('message', 'Empresa eliminada exitosamente');
    }
}
