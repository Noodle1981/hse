<?php

namespace App\Livewire\Users;

use Livewire\Component;

class UserBuilder extends Component
{
    public function render()
    {
        return view('livewire.users.user-builder')
            ->layout('components.layouts.app');
    }
}
