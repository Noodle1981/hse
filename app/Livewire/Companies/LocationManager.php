<?php

namespace App\Livewire\Companies;

use App\Models\Empresa;
use App\Models\Sede;
use App\Models\User;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

class LocationManager extends Component
{
    public Empresa $empresa;
    public $sedes = [];
    
    // Formulario para nueva sede
    public string $newNombreSede = '';
    public string $newDireccion = '';
    public string $newTipoSede = '';
    public string $newRubro = '';
    public string $newLocalidad = '';
    public ?int $newProvinciaId = null;
    public $newLatitud = '';
    public $newLongitud = '';
    public string $newTelefono = '';
    public string $newCorreo = '';

    // Contactos Responsables
    public string $responsable_1_nombre = '';
    public string $responsable_1_cargo = '';
    public string $responsable_1_email = '';
    public string $responsable_2_nombre = '';
    public string $responsable_2_cargo = '';
    public string $responsable_2_email = '';
    public string $responsable_3_nombre = '';
    public string $responsable_3_cargo = '';
    public string $responsable_3_email = '';
    
    public int $contactosVisibles = 1;

    public bool $showForm = false;
    public string $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'showDeleted' => ['except' => false],
    ];

    public function updatingSearch() { $this->resetPage(); }
    
    // Edición
    public ?int $editingId = null;

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

    public function mount(\App\Models\Empresa $empresa)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Tenancy isolation
        if (!$user->isSuperAdmin() && $empresa->consultora_id !== $user->consultora_id) {
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

        if ($this->search) {
            $query->where(function($q) {
                $q->where('nombre_sede', 'like', '%' . $this->search . '%')
                  ->orWhere('localidad', 'like', '%' . $this->search . '%')
                  ->orWhere('direccion', 'like', '%' . $this->search . '%');
            });
        }

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
        $this->showForm = false;
        $this->resetForm();
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
        $this->availableUsers = User::where('rol', 'tecnico')
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
        $this->editingId = null;
        $this->resetForm();
    }

    public function addContacto()
    {
        if ($this->contactosVisibles < 3) {
            $this->contactosVisibles++;
        }
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
            'newRubro' => 'nullable|string|max:100',
            'newLocalidad' => 'nullable|string|max:100',
            'newProvinciaId' => 'nullable|exists:provincias,id',
            'newLatitud' => 'nullable|numeric|between:-90,90',
            'newLongitud' => 'nullable|numeric|between:-180,180',
            'newTelefono' => 'nullable|string|max:50',
            'newCorreo' => 'nullable|email|max:255',
            'responsable_1_nombre' => 'nullable|string|max:255',
            'responsable_1_cargo' => 'nullable|string|max:255',
            'responsable_1_email' => 'nullable|email|max:255',
            'responsable_2_nombre' => 'nullable|string|max:255',
            'responsable_2_cargo' => 'nullable|string|max:255',
            'responsable_2_email' => 'nullable|email|max:255',
            'responsable_3_nombre' => 'nullable|string|max:255',
            'responsable_3_cargo' => 'nullable|string|max:255',
            'responsable_3_email' => 'nullable|email|max:255',
        ], [
            'newNombreSede.unique' => 'Ya existe una sede con este nombre en esta empresa.'
        ]);

        Sede::create([
            'empresa_id' => $this->empresa->id,
            'nombre_sede' => $this->newNombreSede,
            'direccion' => $this->newDireccion ?: null,
            'tipo_sede' => $this->newTipoSede ?: null,
            'rubro' => $this->newRubro ?: null,
            'localidad' => $this->newLocalidad ?: null,
            'provincia_id' => $this->newProvinciaId,
            'latitud' => $this->newLatitud ?: null,
            'longitud' => $this->newLongitud ?: null,
            'telefono' => $this->newTelefono ?: null,
            'correo' => $this->newCorreo ?: null,
            'responsable_1_nombre' => $this->responsable_1_nombre ?: null,
            'responsable_1_cargo' => $this->responsable_1_cargo ?: null,
            'responsable_1_email' => $this->responsable_1_email ?: null,
            'responsable_2_nombre' => $this->responsable_2_nombre ?: null,
            'responsable_2_cargo' => $this->responsable_2_cargo ?: null,
            'responsable_2_email' => $this->responsable_2_email ?: null,
            'responsable_3_nombre' => $this->responsable_3_nombre ?: null,
            'responsable_3_cargo' => $this->responsable_3_cargo ?: null,
            'responsable_3_email' => $this->responsable_3_email ?: null,
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
        $this->newNombreSede = $sede->nombre_sede;
        $this->newDireccion = $sede->direccion ?? '';
        $this->newTipoSede = $sede->tipo_sede ?? '';
        $this->newRubro = $sede->rubro ?? '';
        $this->newLocalidad = $sede->localidad ?? '';
        $this->newProvinciaId = $sede->provincia_id;
        $this->newLatitud = $sede->latitud;
        $this->newLongitud = $sede->longitud;
        
        $this->newTelefono = $sede->telefono ?? '';
        $this->newCorreo = $sede->correo ?? '';
        $this->responsable_1_nombre = $sede->responsable_1_nombre ?? '';
        $this->responsable_1_cargo = $sede->responsable_1_cargo ?? '';
        $this->responsable_1_email = $sede->responsable_1_email ?? '';
        $this->responsable_2_nombre = $sede->responsable_2_nombre ?? '';
        $this->responsable_2_cargo = $sede->responsable_2_cargo ?? '';
        $this->responsable_2_email = $sede->responsable_2_email ?? '';
        $this->responsable_3_nombre = $sede->responsable_3_nombre ?? '';
        $this->responsable_3_cargo = $sede->responsable_3_cargo ?? '';
        $this->responsable_3_email = $sede->responsable_3_email ?? '';
        
        $c3 = ($this->responsable_3_nombre || $this->responsable_3_cargo || $this->responsable_3_email);
        $c2 = ($this->responsable_2_nombre || $this->responsable_2_cargo || $this->responsable_2_email);
        
        if ($c3) {
            $this->contactosVisibles = 3;
        } elseif ($c2) {
            $this->contactosVisibles = 2;
        } else {
            $this->contactosVisibles = 1;
        }
        
        $this->showForm = true;
    }

    public function update()
    {
        $this->validate([
            'newNombreSede' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('sedes', 'nombre_sede')->where(function ($query) {
                    return $query->where('empresa_id', $this->empresa->id);
                })->ignore($this->editingId)
            ],
            'newDireccion' => 'nullable|string|max:255',
            'newTipoSede' => 'nullable|string|max:100',
            'newRubro' => 'nullable|string|max:100',
            'newLocalidad' => 'nullable|string|max:100',
            'newProvinciaId' => 'nullable|exists:provincias,id',
            'newLatitud' => 'nullable|numeric|between:-90,90',
            'newLongitud' => 'nullable|numeric|between:-180,180',
            'newTelefono' => 'nullable|string|max:50',
            'newCorreo' => 'nullable|email|max:255',
            'responsable_1_nombre' => 'nullable|string|max:255',
            'responsable_1_cargo' => 'nullable|string|max:255',
            'responsable_1_email' => 'nullable|email|max:255',
            'responsable_2_nombre' => 'nullable|string|max:255',
            'responsable_2_cargo' => 'nullable|string|max:255',
            'responsable_2_email' => 'nullable|email|max:255',
            'responsable_3_nombre' => 'nullable|string|max:255',
            'responsable_3_cargo' => 'nullable|string|max:255',
            'responsable_3_email' => 'nullable|email|max:255',
        ], [
            'newNombreSede.unique' => 'Ya existe una sede con este nombre en esta empresa.'
        ]);

        $sede = $this->empresa->sedes()->findOrFail($this->editingId);
        $sede->update([
            'nombre_sede' => $this->newNombreSede,
            'direccion' => $this->newDireccion ?: null,
            'tipo_sede' => $this->newTipoSede ?: null,
            'rubro' => $this->newRubro ?: null,
            'localidad' => $this->newLocalidad ?: null,
            'provincia_id' => $this->newProvinciaId,
            'latitud' => $this->newLatitud ?: null,
            'longitud' => $this->newLongitud ?: null,
            'telefono' => $this->newTelefono ?: null,
            'correo' => $this->newCorreo ?: null,
            'responsable_1_nombre' => $this->responsable_1_nombre ?: null,
            'responsable_1_cargo' => $this->responsable_1_cargo ?: null,
            'responsable_1_email' => $this->responsable_1_email ?: null,
            'responsable_2_nombre' => $this->responsable_2_nombre ?: null,
            'responsable_2_cargo' => $this->responsable_2_cargo ?: null,
            'responsable_2_email' => $this->responsable_2_email ?: null,
            'responsable_3_nombre' => $this->responsable_3_nombre ?: null,
            'responsable_3_cargo' => $this->responsable_3_cargo ?: null,
            'responsable_3_email' => $this->responsable_3_email ?: null,
        ]);

        $this->loadSedes();
        $this->cancelEdit();
        $this->showForm = false;
        session()->flash('message', 'Sede actualizada exitosamente');
    }





    private function resetForm()
    {
        $this->newNombreSede = '';
        $this->newDireccion = '';
        $this->newTipoSede = '';
        $this->newRubro = '';
        $this->newLocalidad = '';
        $this->newProvinciaId = null;
        $this->newLatitud = '';
        $this->newLongitud = '';
        $this->newTelefono = '';
        $this->newCorreo = '';
        $this->responsable_1_nombre = '';
        $this->responsable_1_cargo = '';
        $this->responsable_1_email = '';
        $this->responsable_2_nombre = '';
        $this->responsable_2_cargo = '';
        $this->responsable_2_email = '';
        $this->responsable_3_nombre = '';
        $this->responsable_3_cargo = '';
        $this->responsable_3_email = '';
        $this->contactosVisibles = 1;
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.companies.location-manager');
    }
}
