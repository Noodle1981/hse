<?php

namespace App\Livewire\Admin;

use App\Models\Consultora;
use Livewire\Component;
use Livewire\WithFileUploads;

class ConsultantEdit extends Component
{
    use WithFileUploads;

    public Consultora $consultora;
    public $nombre, $cuit, $max_tecnicos;
    public $description, $fiscal_address, $email, $phone;
    public $province, $locality;
    public $logo;
    public $color_primary;
    public $color_secondary;
    public $color_accent;
    public $existingLogo = null;

    public function mount(Consultora $consultant)
    {
        $this->consultora = $consultant;
        
        $this->nombre         = $consultant->nombre;
        $this->cuit           = $consultant->cuit;
        $this->max_tecnicos   = $consultant->max_tecnicos;
        $this->description    = $consultant->description;
        $this->fiscal_address = $consultant->fiscal_address;
        $this->province       = $consultant->province;
        $this->locality       = $consultant->locality;
        $this->email          = $consultant->email;
        $this->phone          = $consultant->phone;
        $this->color_primary  = $consultant->color_primary   ?? '#1e40af';
        $this->color_secondary= $consultant->color_secondary ?? '#6b7280';
        $this->color_accent   = $consultant->color_accent    ?? '#16a34a';
        $this->existingLogo   = $consultant->logo_path;
        $this->logo           = null;
    }

    public function save()
    {
        $this->validate([
            'nombre'          => 'required|string|max:255',
            'cuit'            => 'required|string|max:20|unique:consultoras,cuit,' . $this->consultora->id,
            'max_tecnicos'    => 'required|integer|min:1',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:50',
            'description'     => 'nullable|string',
            'fiscal_address'  => 'nullable|string|max:255',
            'province'        => 'nullable|string|max:100',
            'locality'        => 'nullable|string|max:100',
            'color_primary'   => 'required|string|size:7',
            'color_secondary' => 'required|string|size:7',
            'color_accent'    => 'required|string|size:7',
            'logo'            => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        $data = [
            'nombre'          => $this->nombre,
            'cuit'            => $this->cuit,
            'max_tecnicos'    => $this->max_tecnicos,
            'description'     => $this->description,
            'fiscal_address'  => $this->fiscal_address,
            'province'        => $this->province,
            'locality'        => $this->locality,
            'email'           => $this->email,
            'phone'           => $this->phone,
            'color_primary'   => $this->color_primary,
            'color_secondary' => $this->color_secondary,
            'color_accent'    => $this->color_accent,
        ];

        if ($this->logo) {
            $path = $this->logo->store('logos/consultoras', 'public');
            $data['logo_path'] = $path;
        }

        $this->consultora->update($data);

        session()->flash('notify', 'Consultora editada con éxito');
        return redirect()->route('admin.consultants');
    }

    public function render()
    {
        return view('livewire.admin.consultant-edit')->layout('components.layouts.admin');
    }
}
