<?php

namespace App\Livewire\Epp;

use App\Models\Employee;
use App\Models\Empresa;
use App\Models\Sede;
use Livewire\Component;
use Livewire\WithPagination;

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

    protected $rules = [
        'name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'dni' => 'nullable|string|max:20',
        'cuil' => 'nullable|string|max:20',
        'puesto' => 'nullable|string|max:255',
        'empresa_id' => 'required|exists:empresas,id',
        'sede_id' => 'required|exists:sedes,id',
    ];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingEmpresaFilter() { $this->sedeFilter = ''; $this->resetPage(); }
    public function updatingSedeFilter() { $this->resetPage(); }

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

    public function save()
    {
        $data = $this->validate();

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
        $employees = Employee::query()
            ->with(['empresa', 'sede'])
            ->when($this->search, function($q) {
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
