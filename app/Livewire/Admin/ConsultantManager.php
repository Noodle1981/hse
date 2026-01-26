<?php

namespace App\Livewire\Admin;

use App\Models\Consultora;
use Livewire\Component;
use Livewire\WithPagination;

class ConsultantManager extends Component
{
    use WithPagination;
    use \Livewire\WithFileUploads;

    public $showModal = false;
    public $editingId = null;
    public $nombre, $cuit, $max_tecnicos = 5;
    public $description, $fiscal_address, $email, $phone;
    public $province, $locality;
    public $logo;

    public function render()
    {
        return view('livewire.admin.consultant-manager', [
            'consultoras' => Consultora::withCount(['empresas', 'users'])->paginate(10)
        ])->layout('components.layouts.admin');
    }

    public function create()
    {
        if (Consultora::count() >= 1) {
            $this->dispatch('notify', 'Solo se permite registrar una (1) consultora.');
            return;
        }

        $this->reset(['nombre', 'cuit', 'max_tecnicos', 'editingId', 'description', 'fiscal_address', 'email', 'phone', 'province', 'locality', 'logo']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $consultora = Consultora::findOrFail($id);
        $this->editingId = $id;
        $this->nombre = $consultora->nombre;
        $this->cuit = $consultora->cuit;
        $this->max_tecnicos = $consultora->max_tecnicos;
        $this->description = $consultora->description;
        $this->fiscal_address = $consultora->fiscal_address;
        $this->province = $consultora->province;
        $this->locality = $consultora->locality;
        $this->email = $consultora->email;
        $this->phone = $consultora->phone;
        $this->logo = null; // Reset logo input
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'cuit' => 'required|string|max:20|unique:consultoras,cuit,' . $this->editingId,
            'max_tecnicos' => 'required|integer|min:1',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'fiscal_address' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:100',
            'locality' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:png|max:2048', // 2MB Max, PNG only
        ]);

        if ($this->logo) {
            // Overwrite the public/images/logo.png
            try {
                $path = public_path('images/logo.png');
                file_put_contents($path, file_get_contents($this->logo->getRealPath()));
            } catch (\Exception $e) {
                // Fail silently or notify?
                // For now just proceed
            }
        }

        $data = [
            'nombre' => $this->nombre,
            'cuit' => $this->cuit,
            'max_tecnicos' => $this->max_tecnicos,
            'description' => $this->description,
            'fiscal_address' => $this->fiscal_address,
            'province' => $this->province,
            'locality' => $this->locality,
            'email' => $this->email,
            'phone' => $this->phone,
        ];

        if ($this->editingId) {
            Consultora::find($this->editingId)->update($data);
        } else {
            Consultora::create($data);
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
