<?php

namespace App\Livewire\Companies;

use App\Models\Empresa;
use App\Models\Sede;
use App\Models\User;
use Livewire\Component;
use Illuminate\Validation\Rule;

class LocationManager extends Component
{
    public Empresa $empresa;
    public $sedes = [];
    
    // Formulario para nueva sede
    public string $newNombreSede = '';
    public string $newDireccion = '';
    public string $newTipoSede = '';
    public string $newLocalidad = '';
    public string $newTelefono = '';
    public string $newCorreo = '';
    public bool $showForm = false;
    
    // Edición
    public ?int $editingId = null;
    public string $editNombreSede = '';
    public string $editDireccion = '';
    public string $editTipoSede = '';
    public string $editLocalidad = '';

    // Listas para autocompletado
    public $existingTipos = [];
    public $existingLocalidades = [];

    // Gestión de usuarios
    public ?int $managingUsersForSede = null;
    public $availableUsers = [];
    public $assignedUsers = [];

    public function mount(Empresa $empresa)
    {
        $this->empresa = $empresa;
        $this->loadSedes();
        $this->loadAvailableUsers();
    }

    public function loadSedes()
    {
        $this->sedes = $this->empresa->sedes()->withCount('users')->orderBy('nombre_sede')->get();
        
        // Cargar listas para autocompletado
        $this->existingTipos = Sede::whereNotNull('tipo_sede')
            ->where('tipo_sede', '!=', '')
            ->distinct()
            ->orderBy('tipo_sede')
            ->pluck('tipo_sede')
            ->toArray();
            
        $this->existingLocalidades = Sede::whereNotNull('localidad')
            ->where('localidad', '!=', '')
            ->distinct()
            ->orderBy('localidad')
            ->pluck('localidad')
            ->toArray();
    }

    // Validación en tiempo real para nombre de sede
    public function updatedNewNombreSede($value)
    {
        $this->validateOnly('newNombreSede', [
            'newNombreSede' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('sedes', 'nombre_sede')->where(function ($query) {
                    return $query->where('empresa_id', $this->empresa->id);
                })
            ],
        ], [
            'newNombreSede.unique' => 'Ya existe una sede con este nombre en esta empresa.'
        ]);
    }

    public function loadAvailableUsers()
    {
        // Cargar solo técnicos
        $this->availableUsers = User::where('rol', 'Técnico')
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();
    }

    public function manageUsers(int $sedeId)
    {
        $this->managingUsersForSede = $sedeId;
        $sede = Sede::findOrFail($sedeId);
        $this->assignedUsers = $sede->users()->pluck('users.id')->toArray();
    }

    public function toggleUser(int $userId)
    {
        if (!$this->managingUsersForSede) return;

        $sede = Sede::findOrFail($this->managingUsersForSede);
        
        if (in_array($userId, $this->assignedUsers)) {
            // Desasignar
            $sede->users()->detach($userId);
            $this->assignedUsers = array_diff($this->assignedUsers, [$userId]);
        } else {
            // Asignar
            $sede->users()->attach($userId);
            $this->assignedUsers[] = $userId;
        }

        $this->loadSedes();
        session()->flash('message', 'Asignación actualizada');
    }

    public function closeUserManagement()
    {
        $this->managingUsersForSede = null;
        $this->assignedUsers = [];
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate([
            'newNombreSede' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('sedes', 'nombre_sede')->where(function ($query) {
                    return $query->where('empresa_id', $this->empresa->id);
                })
            ],
            'newDireccion' => 'nullable|string|max:255',
            'newTipoSede' => 'nullable|string|max:100',
            'newLocalidad' => 'nullable|string|max:100',
            'newTelefono' => 'nullable|string|max:50',
            'newCorreo' => 'nullable|email|max:255',
        ], [
            'newNombreSede.unique' => 'Ya existe una sede con este nombre en esta empresa.'
        ]);

        Sede::create([
            'empresa_id' => $this->empresa->id,
            'nombre_sede' => $this->newNombreSede,
            'direccion' => $this->newDireccion ?: null,
            'tipo_sede' => $this->newTipoSede ?: null,
            'localidad' => $this->newLocalidad ?: null,
            'telefono' => $this->newTelefono ?: null,
            'correo' => $this->newCorreo ?: null,
        ]);

        $this->loadSedes();
        $this->resetForm();
        $this->showForm = false;
        session()->flash('message', 'Sede creada exitosamente');
    }

    public function edit(int $id)
    {
        $sede = Sede::findOrFail($id);
        $this->editingId = $id;
        $this->editNombreSede = $sede->nombre_sede;
        $this->editDireccion = $sede->direccion ?? '';
        $this->editTipoSede = $sede->tipo_sede ?? '';
        $this->editLocalidad = $sede->localidad ?? '';
    }

    public function update()
    {
        $this->validate([
            'editNombreSede' => 'required|string|max:255',
            'editDireccion' => 'nullable|string|max:255',
            'editTipoSede' => 'nullable|string|max:100',
            'editLocalidad' => 'nullable|string|max:100',
        ]);

        $sede = Sede::findOrFail($this->editingId);
        $sede->update([
            'nombre_sede' => $this->editNombreSede,
            'direccion' => $this->editDireccion ?: null,
            'tipo_sede' => $this->editTipoSede ?: null,
            'localidad' => $this->editLocalidad ?: null,
        ]);

        $this->loadSedes();
        $this->cancelEdit();
        session()->flash('message', 'Sede actualizada exitosamente');
    }

    public function delete(int $id)
    {
        $sede = Sede::findOrFail($id);
        $sede->delete();
        $this->loadSedes();
        session()->flash('message', 'Sede eliminada exitosamente');
    }

    public function cancelEdit()
    {
        $this->editingId = null;
        $this->editNombreSede = '';
        $this->editDireccion = '';
        $this->editTipoSede = '';
        $this->editLocalidad = '';
    }

    private function resetForm()
    {
        $this->newNombreSede = '';
        $this->newDireccion = '';
        $this->newTipoSede = '';
        $this->newLocalidad = '';
        $this->newTelefono = '';
        $this->newCorreo = '';
    }

    public function render()
    {
        return view('livewire.companies.location-manager')
            ->layout('components.layouts.app');
    }
}
