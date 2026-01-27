<?php

namespace App\Livewire\Companies;

use App\Models\Empresa;
use Livewire\Component;
use Illuminate\Validation\Rule;

class CompanyBuilder extends Component
{
    public ?Empresa $empresa = null;
    public string $nombre = '';
    public string $razonSocial = '';
    public string $cuit = '';

    protected function rules(): array
    {
        $consultoraId = auth()->user()->consultora_id;

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('empresas', 'nombre')
                    ->where(function ($query) use ($consultoraId) {
                        return $query->where('consultora_id', $consultoraId);
                    })
                    ->ignore($this->empresa->id ?? null)
            ],
            'razonSocial' => 'required|string|max:255',
            'cuit' => [
                'required',
                'string',
                'max:13',
                Rule::unique('empresas', 'cuit')
                    ->where(function ($query) use ($consultoraId) {
                        return $query->where('consultora_id', $consultoraId);
                    })
                    ->ignore($this->empresa->id ?? null)
            ],
        ];
    }

    protected $validationAttributes = [
        'nombre' => 'nombre',
        'razonSocial' => 'razón social',
        'cuit' => 'CUIT',
    ];

    public function mount(?int $id = null)
    {
        if ($id) {
            $this->empresa = Empresa::findOrFail($id);
            $this->nombre = $this->empresa->nombre;
            $this->razonSocial = $this->empresa->razon_social;
            $this->cuit = $this->empresa->cuit;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'nombre' => $this->nombre,
            'razon_social' => $this->razonSocial,
            'cuit' => $this->cuit,
        ];
        
        // Tenancy: Assign consultora_id if not Super Admin
        if (auth()->check() && !auth()->user()->isSuperAdmin()) {
            $data['consultora_id'] = auth()->user()->consultora_id;
        }

        if ($this->empresa) {
            $this->empresa->update($data);
            $message = 'Empresa actualizada exitosamente';
        } else {
            Empresa::create($data);
            $message = 'Empresa creada exitosamente';
        }

        session()->flash('message', $message);
        return $this->redirect(route('gestor.companies.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.companies.company-builder')
            ->layout('components.layouts.app');
    }
}
