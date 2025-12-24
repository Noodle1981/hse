<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Iniciar Sesión')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];

    protected $messages = [
        'email.required' => 'El correo es requerido',
        'email.email' => 'Ingrese un correo válido',
        'password.required' => 'La contraseña es requerida',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            
            return redirect()->intended('/dashboard');
        }

        $this->addError('email', 'Las credenciales no son válidas.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
