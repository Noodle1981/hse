<?php

namespace App\Livewire\Epp;

use App\Models\EppItem;
use Livewire\Component;
use Livewire\WithPagination;

class EppCatalog extends Component
{
    use WithPagination;

    public $search = '';
    public $showForm = false;
    public $itemId = null;

    // Campos del formulario
    public $nombre, $descripcion, $tipo, $vida_util_meses, $codigo_norma;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'tipo' => 'required|string',
        'vida_util_meses' => 'nullable|integer|min:1',
        'codigo_norma' => 'nullable|string|max:100',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $item = EppItem::findOrFail($id);
        $this->itemId = $item->id;
        $this->nombre = $item->nombre;
        $this->descripcion = $item->descripcion;
        $this->tipo = $item->tipo;
        $this->vida_util_meses = $item->vida_util_meses;
        $this->codigo_norma = $item->codigo_norma;
        $this->showForm = true;
    }

    public function save()
    {
        $data = $this->validate();

        if ($this->itemId) {
            EppItem::find($this->itemId)->update($data);
            session()->flash('message', 'Elemento actualizado correctamente.');
        } else {
            EppItem::create($data);
            session()->flash('message', 'Elemento creado correctamente.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        EppItem::find($id)->delete();
        session()->flash('message', 'Elemento eliminado.');
    }

    private function resetForm()
    {
        $this->itemId = null;
        $this->nombre = '';
        $this->descripcion = '';
        $this->tipo = '';
        $this->vida_util_meses = null;
        $this->codigo_norma = '';
    }

    public function render()
    {
        $items = EppItem::query()
            ->when($this->search, fn($q) => $q->where('nombre', 'like', '%' . $this->search . '%')
                                               ->orWhere('tipo', 'like', '%' . $this->search . '%'))
            ->latest()
            ->paginate(10);

        return view('livewire.epp.epp-catalog', [
            'items' => $items,
        ])->layout('components.layouts.app');
    }
}
