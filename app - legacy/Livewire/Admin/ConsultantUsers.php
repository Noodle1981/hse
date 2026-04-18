<?php

namespace App\Livewire\Admin;

use App\Models\Consultora;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class ConsultantUsers extends Component
{
    use WithPagination;

    public Consultora $consultant;
    public $showModal = false;
    
    public $editingId = null;

    // Form fields
    public $nombres;
    public $apellidos;
    public $email;
    public $password;
    public $rol = 'gestor'; // Default role
    public $estado = 'activo';

    public function mount(Consultora $consultant)
    {
        $this->consultant = $consultant;
    }

    public function render()
    {
        return view('livewire.admin.consultant-users', [
            'users' => $this->consultant->users()->paginate(10)
        ]);
    }

    public function create()
    {
        $this->reset(['nombres', 'apellidos', 'email', 'password', 'rol', 'editingId']);
        $this->rol = 'gestor';
        $this->estado = 'activo';
        $this->showModal = true;
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->editingId = $id;
        $this->nombres = $user->nombres;
        $this->apellidos = $user->apellidos;
        $this->email = $user->email;
        $this->rol = $user->rol;
        $this->estado = $user->estado;
        $this->password = ''; // Don't fill password
        $this->showModal = true;
    }

    public function save()
    {
        $rules = [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->editingId,
            'rol' => 'required|string|in:gestor,tecnico',
        ];

        if (!$this->editingId) {
            $rules['password'] = 'required|string|min:8';
        } else {
            $rules['password'] = 'nullable|string|min:8';
        }

        $this->validate($rules);

        $data = [
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'email' => $this->email,
            'rol' => $this->rol,
            'estado' => 'activo',
            'consultora_id' => $this->consultant->id,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
            $data['must_change_password'] = true; // Obliga a cambiar contraseña en el próximo login
        }

        if ($this->editingId) {
            User::find($this->editingId)->update($data);
            $message = 'Usuario actualizado con éxito';
        } else {
            $data['must_change_password'] = true; // Siempre el primer login
            User::create($data);
            $message = 'Usuario creado con éxito';
        }

        $this->showModal = false;
        $this->dispatch('notify', $message);
    }
}
