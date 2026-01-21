<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = 'all';
    
    // Formulario
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $nombres = '';
    public string $apellidos = '';
    public string $email = '';
    public string $password = '';
    public string $rol = 'Técnico';
    public string $matricula = '';
    public string $telefono = '';
    public string $estado = 'activo';

    // Modal de confirmación de eliminación
    public bool $confirmingDeletion = false;
    public ?int $userToDeleteId = null;
    public string $userToDeleteName = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedRoleFilter()
    {
        $this->resetPage();
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetForm();
        }
    }

    public function edit(int $id)
    {
        $user = User::findOrFail($id);
        $this->editingId = $id;
        $this->nombres = $user->nombres;
        $this->apellidos = $user->apellidos;
        $this->email = $user->email;
        $this->rol = $user->rol;
        $this->matricula = $user->matricula ?? '';
        $this->telefono = $user->telefono ?? '';
        $this->estado = $user->estado;
        $this->password = ''; // No mostrar password
        $this->showForm = true;
    }

    public function save()
    {
        $rules = [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->editingId)],
            'rol' => 'required|in:Administrador,Técnico',
            'matricula' => 'nullable|string|max:50',
            'telefono' => 'nullable|string|max:50',
            'estado' => 'required|in:activo,inactivo',
        ];

        if (!$this->editingId) {
            $rules['password'] = 'required|string|min:6';
        } else {
            $rules['password'] = 'nullable|string|min:6';
        }

        $this->validate($rules);

        $data = [
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'email' => $this->email,
            'rol' => $this->rol,
            'matricula' => $this->matricula ?: null,
            'telefono' => $this->telefono ?: null,
            'estado' => $this->estado,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editingId) {
            User::findOrFail($this->editingId)->update($data);
            session()->flash('message', 'Usuario actualizado exitosamente');
        } else {
            User::create($data);
            session()->flash('message', 'Usuario creado exitosamente');
        }

        $this->resetForm();
        $this->showForm = false;
        $this->resetPage();
    }

    public function confirmDelete(int $id, string $name)
    {
        // No permitir eliminar al usuario actual
        if ($id === auth()->id()) {
            session()->flash('error', 'No puedes eliminar tu propio usuario');
            return;
        }

        $this->userToDeleteId = $id;
        $this->userToDeleteName = $name;
        $this->confirmingDeletion = true;
    }

    public function cancelDelete()
    {
        $this->confirmingDeletion = false;
        $this->userToDeleteId = null;
        $this->userToDeleteName = '';
    }

    public function delete()
    {
        if (!$this->userToDeleteId) return;

        User::findOrFail($this->userToDeleteId)->delete();
        
        $this->cancelDelete();
        session()->flash('message', 'Usuario dado de baja exitosamente. (Datos históricos preservados)');
        $this->resetPage();
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->nombres = '';
        $this->apellidos = '';
        $this->email = '';
        $this->password = '';
        $this->rol = 'Técnico';
        $this->matricula = '';
        $this->telefono = '';
        $this->estado = 'activo';
    }

    public function render()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('nombres', 'like', "%{$this->search}%")
                  ->orWhere('apellidos', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('matricula', 'like', "%{$this->search}%");
            });
        }

        if ($this->roleFilter !== 'all') {
            $query->where('rol', $this->roleFilter);
        }

        $users = $query->orderBy('apellidos')->orderBy('nombres')->paginate(15);

        return view('livewire.users.user-manager', [
            'users' => $users,
        ])->layout('components.layouts.app');
    }
}
