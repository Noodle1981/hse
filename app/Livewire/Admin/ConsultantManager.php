<?php

namespace App\Livewire\Admin;

use App\Models\Consultora;
use Livewire\Component;
use Livewire\WithPagination;

class ConsultantManager extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.admin.consultant-manager', [
            'consultoras' => Consultora::withCount(['empresas', 'users'])->paginate(10)
        ])->layout('components.layouts.admin');
    }

    public function delete($id)
    {
        Consultora::find($id)->delete();
        $this->dispatch('notify', 'Consultora eliminada');
    }
}
