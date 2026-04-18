<?php

namespace App\Livewire\Admin;

use App\Models\Consultora;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class ConsultantManager extends Component
{
    use WithPagination;

    #[Layout('components.layouts.admin')]
    public function render()
    {
        return view('livewire.admin.consultant-manager', [
            'consultoras' => Consultora::withCount(['empresas', 'users'])->paginate(10)
        ]);
    }

    public function delete($id)
    {
        Consultora::find($id)->delete();
        $this->dispatch('notify', 'Consultora eliminada');
    }
}
