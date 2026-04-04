<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class MyProfile extends Component
{
    public $nombres;
    public $apellidos;
    public $email;
    public $telefono;
    public $matricula;
    
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $this->nombres = $user->nombres;
        $this->apellidos = $user->apellidos;
        $this->email = $user->email;
        $this->telefono = $user->telefono;
        $this->matricula = $user->matricula;
    }

    public function updateProfile()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $this->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:50',
        ]);

        $user->update([
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'telefono' => $this->telefono,
        ]);

        session()->flash('message', 'Perfil actualizado correctamente.');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.current_password' => 'La contraseña actual no es correcta.',
            'new_password.required' => 'La nueva contraseña es obligatoria.',
            'new_password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 6 caracteres.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update([
            'password' => Hash::make($this->new_password)
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        session()->flash('password_message', 'Contraseña actualizada exitosamente.');
    }

    public function render()
    {
        return view('livewire.auth.my-profile');
    }
}
