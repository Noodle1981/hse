<?php

namespace App\Livewire\Epp;

use App\Models\EppItem;
use Livewire\Component;
use Livewire\WithPagination;

class EppCatalog extends Component
{
    use WithPagination;

    // Catálogo de elementos EPP predefinidos
    const EPP_CATALOG_OPTIONS = [
        'Cabeza' => [
            'Casco de seguridad',
            'Casco con barbiquejo',
            'Gorro desechable',
        ],
        'Auditiva' => [
            'Tapones auditivos',
            'Orejeras',
            'Tapones con cordón',
        ],
        'Ocular' => [
            'Gafas de seguridad',
            'Antiparras',
            'Careta facial',
            'Gafas para soldar',
        ],
        'Respiratoria' => [
            'Barbijo descartable',
            'Respirador N95',
            'Máscara con filtro',
            'Respirador media cara',
        ],
        'Manos' => [
            'Guantes de látex',
            'Guantes de nitrilo',
            'Guantes de cuero',
            'Guantes anticorte',
            'Guantes dieléctricos',
        ],
        'Calzado' => [
            'Botas de seguridad',
            'Zapatos con puntera',
            'Botas dieléctricas',
            'Calzado antideslizante',
        ],
        'Cuerpo' => [
            'Chaleco reflectivo',
            'Mameluco',
            'Delantal',
            'Arnés de seguridad',
            'Faja lumbar',
        ],
        'Caídas' => [
            'Arnés anticaída',
            'Línea de vida',
            'Cabo de amarre',
            'Mosquetón',
        ],
    ];

    public $search = '';
    public $showForm = false;
    public $itemId = null;

    // Campos del formulario
    public $nombre, $descripcion, $tipo, $vida_util_meses, $codigo_norma, $stock_actual;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'tipo' => 'required|string',
        'vida_util_meses' => 'nullable|integer|min:1',
        'codigo_norma' => 'nullable|string|max:100',
        'stock_actual' => 'required|integer|min:0',
    ];

    public function getEppCatalogOptions()
    {
        return self::EPP_CATALOG_OPTIONS;
    }

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
        $this->stock_actual = $item->stock_actual;
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
        $this->stock_actual = 0;
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
