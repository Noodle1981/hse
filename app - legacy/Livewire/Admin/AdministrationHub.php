<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class AdministrationHub extends Component
{
    public function render()
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        $consultoraName = $user->consultora?->nombre ?? 'XAMASAFE';

        return view('livewire.admin.administration-hub', [
            'consultoraName' => $consultoraName
        ]);
    }
}
