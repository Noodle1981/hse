<?php

namespace App\Livewire\Epp;

use App\Models\Employee;
use App\Models\Empresa;
use App\Models\Sede;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class EmployeeManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showForm = false;
    public $employeeId = null;

    // Filtros
    public $empresaFilter = '';
    public $sedeFilter = '';

    // Campos del formulario
    public $name, $last_name, $dni, $cuil, $puesto, $empresa_id, $sede_id;

    // Soft Deletes
    public bool $showDeleted = false;
    public bool $confirmingDeletion = false;
    public ?int $employeeToDeleteId = null;
    public string $employeeToDeleteName = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dni' => [
                'nullable', 
                'string', 
                'max:20', 
                Rule::unique('employees', 'dni')->ignore($this->employeeId)
            ],
            'cuil' => [
                'nullable', 
                'string', 
                'max:20', 
                Rule::unique('employees', 'cuil')->ignore($this->employeeId)
            ],
            'puesto' => 'nullable|string|max:255',
            'empresa_id' => 'required|exists:empresas,id',
            'sede_id' => 'required|exists:sedes,id',
        ];
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingEmpresaFilter() { $this->sedeFilter = ''; $this->resetPage(); }
    public function updatingSedeFilter() { $this->resetPage(); }
    public function updatedShowDeleted() { $this->resetPage(); }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $emp = Employee::findOrFail($id);
        $this->employeeId = $emp->id;
        $this->name = $emp->name;
        $this->last_name = $emp->last_name;
        $this->dni = $emp->dni;
        $this->cuil = $emp->cuil;
        $this->puesto = $emp->puesto;
        $this->empresa_id = $emp->empresa_id;
        $this->sede_id = $emp->sede_id;
        $this->showForm = true;
    }

    public function confirmDelete($id, $name)
    {
        $this->employeeToDeleteId = $id;
        $this->employeeToDeleteName = $name;
        $this->confirmingDeletion = true;
    }

    public function cancelDelete()
    {
        $this->confirmingDeletion = false;
        $this->employeeToDeleteId = null;
        $this->employeeToDeleteName = '';
    }

    public function delete()
    {
        if (!$this->employeeToDeleteId) return;

        $employee = Employee::findOrFail($this->employeeToDeleteId);
        $employee->delete();
        
        $this->cancelDelete();
        session()->flash('message', 'Empleado dado de baja exitosamente.');
    }

    public function restore($id)
    {
        $employee = Employee::withTrashed()->findOrFail($id);
        $employee->restore();
        
        session()->flash('message', 'Empleado restaurado exitosamente.');
    }

    public function save()
    {
        $data = $this->validate();

        // Convertir vacíos a null para evitar error de constraint unique
        $data['dni'] = $data['dni'] ?: null;
        $data['cuil'] = $data['cuil'] ?: null;

        if ($this->employeeId) {
            Employee::find($this->employeeId)->update($data);
            session()->flash('message', 'Empleado actualizado correctamente.');
        } else {
            Employee::create($data);
            session()->flash('message', 'Empleado creado correctamente.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->employeeId = null;
        $this->name = '';
        $this->last_name = '';
        $this->dni = '';
        $this->cuil = '';
        $this->puesto = '';
        $this->empresa_id = '';
        $this->sede_id = '';
    }

    public function render()
    {
        $query = Employee::query()
            ->with(['empresa', 'sede']);

        if ($this->showDeleted) {
            $query->onlyTrashed();
        }

        $employees = $query->when($this->search, function($q) {
                $q->where(function($sq) {
                    $sq->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('last_name', 'like', '%' . $this->search . '%')
                      ->orWhere('dni', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->empresaFilter, fn($q) => $q->where('empresa_id', $this->empresaFilter))
            ->when($this->sedeFilter, fn($q) => $q->where('sede_id', $this->sedeFilter))
            ->latest()
            ->paginate(15);

        $empresas = Empresa::orderBy('nombre')->get();
        $sedesFilter = $this->empresaFilter 
            ? Sede::where('empresa_id', $this->empresaFilter)->orderBy('nombre_sede')->get() 
            : collect();

        $formSedes = $this->empresa_id 
            ? Sede::where('empresa_id', $this->empresa_id)->orderBy('nombre_sede')->get() 
            : collect();

        return view('livewire.epp.employee-manager', [
            'employees' => $employees,
            'empresas' => $empresas,
            'sedesFilter' => $sedesFilter,
            'formSedes' => $formSedes,
        ])->layout('components.layouts.app');
    }
}
