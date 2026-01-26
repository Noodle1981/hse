<?php

namespace App\Livewire\Admin;

use App\Models\Consultora;
use Livewire\Component;
use Livewire\WithPagination;

class ConsultantManager extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingId = null;
    public $nombre, $cuit, $max_tecnicos = 5;

    public function render()
    {
        return view('livewire.admin.consultant-manager', [
            'consultoras' => Consultora::withCount(['empresas', 'users'])->paginate(10)
        ])->layout('components.layouts.admin');
    }

    public function create()
    {
        $this->reset(['nombre', 'cuit', 'max_tecnicos', 'editingId']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $consultora = Consultora::findOrFail($id);
        $this->editingId = $id;
        $this->nombre = $consultora->nombre;
        $this->cuit = $consultora->cuit;
        $this->max_tecnicos = $consultora->max_tecnicos;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'cuit' => 'required|string|max:20|unique:consultoras,cuit,' . $this->editingId,
            'max_tecnicos' => 'required|integer|min:1',
        ]);

        if ($this->editingId) {
            Consultora::find($this->editingId)->update([
                'nombre' => $this->nombre,
                'cuit' => $this->cuit,
                'max_tecnicos' => $this->max_tecnicos,
            ]);
        } else {
            Consultora::create([
                'nombre' => $this->nombre,
                'cuit' => $this->cuit,
                'max_tecnicos' => $this->max_tecnicos,
            ]);
        }

        $this->showModal = false;
        $this->dispatch('notify', 'Consultora guardada con éxito');
    }

    public function delete($id)
    {
        Consultora::find($id)->delete();
        $this->dispatch('notify', 'Consultora eliminada');
    }
}
