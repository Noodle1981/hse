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
    public ?int $newProvinciaId = null;
    public $newLatitud = '';
    public $newLongitud = '';
    public string $newTelefono = '';
    public string $newCorreo = '';
    public bool $showForm = false;
    
    // Edición
    public ?int $editingId = null;
    public string $editNombreSede = '';
    public string $editDireccion = '';
    public string $editTipoSede = '';
    public string $editLocalidad = '';
    public ?int $editProvinciaId = null;
    public $editLatitud = '';
    public $editLongitud = '';

    // Listas para autocompletado
    public $existingTipos = [];
    public $existingLocalidades = [];
    public $existingProvincias = [];

    // Gestión de usuarios
    public ?int $managingUsersForSede = null;
    public $availableUsers = [];
    public $assignedUsers = [];

    // Soft Deletes
    public bool $showDeleted = false;
    public bool $confirmingDeletion = false;
    public ?int $sedeToDeleteId = null;
    public string $sedeToDeleteName = '';

    public function mount(Empresa $empresa)
    {
        // Tenancy isolation
        if (!auth()->user()->isSuperAdmin() && $empresa->consultora_id !== auth()->user()->consultora_id) {
            abort(403, 'No tienes permiso para gestionar sedes de esta empresa.');
        }

        $this->empresa = $empresa;
        $this->loadSedes();
        $this->loadAvailableUsers();
        $this->existingProvincias = \App\Models\Provincia::orderBy('nombre')->get();
    }

    public function updatedShowDeleted()
    {
        $this->loadSedes();
    }

    public function loadSedes()
    {
        $query = $this->empresa->sedes()->with(['provincia', 'users'])->withCount('users');

        if ($this->showDeleted) {
            $query->onlyTrashed();
        }

        $this->sedes = $query->orderBy('nombre_sede')->get();
        
        // Cargar listas para autocompletado (solo de activas para mantener limpieza)
        $this->existingTipos = Sede::where('empresa_id', $this->empresa->id)
            ->whereNotNull('tipo_sede')
            ->where('tipo_sede', '!=', '')
            ->distinct()
            ->orderBy('tipo_sede')
            ->pluck('tipo_sede')
            ->toArray();
            
        $this->existingLocalidades = Sede::where('empresa_id', $this->empresa->id)
            ->whereNotNull('localidad')
            ->where('localidad', '!=', '')
            ->distinct()
            ->orderBy('localidad')
            ->pluck('localidad')
            ->toArray();
    }

    // ... (rest of methods until delete) ...

    public function confirmDelete($id, $name)
    {
        $this->sedeToDeleteId = $id;
        $this->sedeToDeleteName = $name;
        $this->confirmingDeletion = true;
    }

    public function cancelDelete()
    {
        $this->confirmingDeletion = false;
        $this->sedeToDeleteId = null;
        $this->sedeToDeleteName = '';
    }

    public function delete()
    {
        if (!$this->sedeToDeleteId) return;

        $sede = $this->empresa->sedes()->findOrFail($this->sedeToDeleteId);
        $sede->delete();
        
        $this->loadSedes();
        $this->cancelDelete();
        session()->flash('message', 'Sede dada de baja exitosamente');
    }

    public function restore($id)
    {
        $sede = $this->empresa->sedes()->withTrashed()->findOrFail($id);
        $sede->restore();
        
        $this->loadSedes();
        session()->flash('message', 'Sede restaurada exitosamente');
    }

    public function cancelEdit()
    {
        $this->editingId = null;
        $this->editNombreSede = '';
        $this->editDireccion = '';
        $this->editTipoSede = '';
        $this->editLocalidad = '';
        $this->editProvinciaId = null;
        $this->editLatitud = '';
        $this->editLongitud = '';
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
        $sede = $this->empresa->sedes()->findOrFail($sedeId);
        $this->assignedUsers = $sede->users()->pluck('users.id')->toArray();
    }

    public function toggleUser(int $userId)
    {
        if (!$this->managingUsersForSede) return;

        $sede = $this->empresa->sedes()->findOrFail($this->managingUsersForSede);
        
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
            'newProvinciaId' => 'nullable|exists:provincias,id',
            'newLatitud' => 'nullable|numeric|between:-90,90',
            'newLongitud' => 'nullable|numeric|between:-180,180',
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
            'provincia_id' => $this->newProvinciaId,
            'latitud' => $this->newLatitud ?: null,
            'longitud' => $this->newLongitud ?: null,
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
        $sede = $this->empresa->sedes()->findOrFail($id);
        $this->editingId = $id;
        $this->editNombreSede = $sede->nombre_sede;
        $this->editDireccion = $sede->direccion ?? '';
        $this->editTipoSede = $sede->tipo_sede ?? '';
        $this->editLocalidad = $sede->localidad ?? '';
        $this->editProvinciaId = $sede->provincia_id;
        $this->editLatitud = $sede->latitud;
        $this->editLongitud = $sede->longitud;
    }

    public function update()
    {
        $this->validate([
            'editNombreSede' => 'required|string|max:255',
            'editDireccion' => 'nullable|string|max:255',
            'editTipoSede' => 'nullable|string|max:100',
            'editLocalidad' => 'nullable|string|max:100',
            'editProvinciaId' => 'nullable|exists:provincias,id',
            'editLatitud' => 'nullable|numeric|between:-90,90',
            'editLongitud' => 'nullable|numeric|between:-180,180',
        ]);

        $sede = $this->empresa->sedes()->findOrFail($this->editingId);
        $sede->update([
            'nombre_sede' => $this->editNombreSede,
            'direccion' => $this->editDireccion ?: null,
            'tipo_sede' => $this->editTipoSede ?: null,
            'localidad' => $this->editLocalidad ?: null,
            'provincia_id' => $this->editProvinciaId,
            'latitud' => $this->editLatitud ?: null,
            'longitud' => $this->editLongitud ?: null,
        ]);

        $this->loadSedes();
        $this->cancelEdit();
        session()->flash('message', 'Sede actualizada exitosamente');
    }





    private function resetForm()
    {
        $this->newNombreSede = '';
        $this->newDireccion = '';
        $this->newTipoSede = '';
        $this->newLocalidad = '';
        $this->newProvinciaId = null;
        $this->newLatitud = '';
        $this->newLongitud = '';
        $this->newTelefono = '';
        $this->newCorreo = '';
    }

    public function render()
    {
        return view('livewire.companies.location-manager')
            ->layout('components.layouts.app');
    }
}
