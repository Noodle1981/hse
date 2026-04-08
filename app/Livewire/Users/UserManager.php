<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class UserManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = 'all';
    public bool $showDeleted = false; // Toggle para ver eliminados

    public function updatedShowDeleted()
    {
        $this->resetPage();
    }
    
    // Formulario
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $nombres = '';
    public string $apellidos = '';
    public string $email = '';
    public string $password = '';
    public string $rol = 'tecnico';
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
        if ($this->showForm) {
            $this->resetForm();
        }
    }

    private function ensureTenancy(User $user)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        if (!$currentUser->isSuperAdmin() && $user->consultora_id !== $currentUser->consultora_id) {
            abort(403, 'Acceso no autorizado a este usuario.');
        }
    }

    public function edit(int $id)
    {
        $user = User::findOrFail($id);
        $this->ensureTenancy($user);
        
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
            'rol' => 'required|in:gestor,tecnico',
            'matricula' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($this->editingId)],
            'telefono' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($this->editingId)],
            'estado' => 'required|in:activo,inactivo',
        ];

        if (!$this->editingId) {
            $rules['password'] = 'required|string|min:6';
        } else {
            $rules['password'] = 'nullable|string|min:6';
        }

        $this->validate($rules);

        if ($this->editingId) {
            $user = User::findOrFail($this->editingId);
            $this->ensureTenancy($user);
        }

        $data = [
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'email' => $this->email,
            'rol' => $this->rol,
            'matricula' => $this->matricula ?: null,
            'telefono' => $this->telefono ?: null,
            'estado' => $this->estado,
        ];

        // Assign to current user's consultancy if not set (Tenancy)
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        if (!$currentUser->isSuperAdmin()) {
            $data['consultora_id'] = $currentUser->consultora_id;
        }

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editingId) {
            $user->update($data); // Use the retrieved user instance
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
        if ($id === Auth::id()) {
            session()->flash('error', 'No puedes eliminar tu propio usuario');
            return;
        }

        $user = User::findOrFail($id);
        $this->ensureTenancy($user);

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
        
        $user = User::findOrFail($this->userToDeleteId);
        $this->ensureTenancy($user);
        
        $user->update(['estado' => 'inactivo']); // Set status to inactive before soft delete
        $user->delete();
        
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
        $this->rol = 'tecnico';
        $this->matricula = '';
        $this->telefono = '';
        $this->estado = 'activo';
    }

    public function render()
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        $query = User::query();

        // 1. Tenancy: Filter by consultancy if not Super Admin
        if (!$currentUser->isSuperAdmin()) {
            $query->where('consultora_id', $currentUser->consultora_id);
        }

        // 2. Toggled Trash View
        if ($this->showDeleted) {
            $query->onlyTrashed();
        }

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
        ]);
    }

    public function restore(int $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $this->ensureTenancy($user);
        
        $user->restore();
        $user->update(['estado' => 'activo']); // Automatically set back to active
        
        session()->flash('message', 'Usuario restaurado exitosamente.');
        $this->resetPage();
    }
}
