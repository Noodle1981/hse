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

    public bool $needsPasswordChange = false;
    public ?int $pendingUserId = null;
    public string $new_password = '';
    public string $new_password_confirmation = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    protected $messages = [
        'email.required' => 'El correo es requerido',
        'email.email' => 'Ingrese un correo válido',
        'password.required' => 'La contraseña es requerida',
        'new_password.required' => 'La nueva contraseña es requerida',
        'new_password.min' => 'La nueva contraseña debe tener al menos 6 caracteres',
        'new_password.confirmed' => 'Las contraseñas no coinciden',
    ];

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = ['email' => $this->email, 'password' => $this->password];
        $user = \App\Models\User::where('email', $this->email)->first();

        // Si tenemos borrado lógico o estado inactivo, es buena práctica comprobarlo.
        // Pero para no romper si no hay estado, solo checkeamos si estado existe
        if ($user && $user->estado === 'inactivo') {
            $this->addError('email', 'El usuario se encuentra inactivo.');
            return;
        }

        if (Auth::validate($credentials)) {
            // El super_admin nunca es forzado a cambiar contraseña (se gestiona fuera del panel)
            $requiresChange = $user->must_change_password && !$user->isSuperAdmin();

            if ($requiresChange) {
                $this->pendingUserId = $user->id;
                $this->needsPasswordChange = true;
                return;
            }

            Auth::login($user, $this->remember);
            return $this->redirectUser($user);
        }

        $this->addError('email', 'Las credenciales no son válidas.');
    }

    public function updatePassword()
    {
        $this->validate([
            'new_password' => 'required|min:6|confirmed',
        ]);

        if (!$this->pendingUserId) {
            return;
        }

        $user = \App\Models\User::find($this->pendingUserId);
        if ($user) {
            $user->update([
                'password' => \Illuminate\Support\Facades\Hash::make($this->new_password),
                'must_change_password' => false,
            ]);

            Auth::login($user, $this->remember);
            return $this->redirectUser($user);
        }
    }

    private function redirectUser($user)
    {
        session()->regenerate();

        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isTechnician()) {
            return redirect()->route('tecnico.checklists.tasks.index');
        }

        return redirect()->route('gestor.visits.history');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
