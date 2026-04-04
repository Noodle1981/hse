<?php

namespace App\Livewire\Companies;

use App\Models\Empresa;
use Livewire\Component;
use Livewire\WithFileUploads;

class CompanyBranding extends Component
{
    use WithFileUploads;

    public $empresa;
    public $nombre, $primary_color;
    public $logo;
    public $existingLogo;

    public function mount($id)
    {
        $this->empresa = Empresa::findOrFail($id);
        $this->nombre = $this->empresa->nombre;
        $this->primary_color = $this->empresa->primary_color ?? '#F69239';
        $this->existingLogo = $this->empresa->logo_path;
    }

    public function save()
    {
        $this->validate([
            'primary_color' => 'required|string|size:7',
            'logo' => 'nullable|image|max:1024', // 1MB Max
        ]);

        $data = [
            'primary_color' => $this->primary_color,
        ];

        if ($this->logo) {
            $path = $this->logo->store('logos', 'public');
            $data['logo_path'] = $path;
        }

        $this->empresa->update($data);

        $this->dispatch('notify', 'Personalización guardada con éxito');
    }

    public function render()
    {
        return view('livewire.companies.company-branding');
    }
}
