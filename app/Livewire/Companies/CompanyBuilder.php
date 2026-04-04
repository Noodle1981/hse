<?php

namespace App\Livewire\Companies;

use App\Models\Empresa;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class CompanyBuilder extends Component
{
    public ?Empresa $empresa = null;
    public string $nombre = '';
    public string $razonSocial = '';
    public string $cuit = '';

    protected function rules(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $consultoraId = $user->consultora_id;

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
            $query = Empresa::query();
            
            // Tenancy isolation
            /** @var \App\Models\User $user */
            $user = Auth::user();
            if (!$user->isSuperAdmin()) {
                $query->where('consultora_id', $user->consultora_id);
            }

            $this->empresa = $query->find($id);
            abort_if(!$this->empresa, 404);
            
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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (Auth::check() && !$user->isSuperAdmin()) {
            $data['consultora_id'] = $user->consultora_id;
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
        return view('livewire.companies.company-builder');
    }
}
