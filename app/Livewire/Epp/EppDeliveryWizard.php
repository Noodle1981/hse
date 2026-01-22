<?php

namespace App\Livewire\Epp;

use App\Models\Employee;
use App\Models\Empresa;
use App\Models\EppDelivery;
use App\Models\EppItem;
use App\Models\Sede;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class EppDeliveryWizard extends Component
{
    public $step = 1;

    // Filtros Paso 1
    public $empresa_id, $sede_id;
    public $searchEmployee = '';

    // Selección Paso 2
    public $selectedEmployeeId;
    public $selectedItems = []; // [item_id => ['tipo_entrega' => 'inicial', 'cantidad' => 1]]

    // Paso 3: Firma
    public $signature;
    public $observaciones;

    public function mount()
    {
        // Si viene un request_id, pre-cargar los datos
        $requestId = request()->query('request_id');
        if ($requestId) {
            $eppRequest = \App\Models\EppDeliveryRequest::find($requestId);
            if ($eppRequest) {
                $this->empresa_id = $eppRequest->empresa_id;
                $this->sede_id = $eppRequest->sede_id;
                $this->selectedEmployeeId = $eppRequest->employee_id;
                $this->selectedItems = $eppRequest->items;
                $this->observaciones = $eppRequest->notes;
                $this->step = 2; // Ir directo al paso de confirmación
            }
        }
    }

    public function selectEmployee($id)
    {
        $this->selectedEmployeeId = $id;
        $this->step = 2;
    }

    public function toggleItem($id)
    {
        if (isset($this->selectedItems[$id])) {
            unset($this->selectedItems[$id]);
        } else {
            $this->selectedItems[$id] = [
                'tipo_entrega' => 'inicial',
                'cantidad' => 1
            ];
        }
    }

    public function save()
    {
        $this->validate([
            'selectedEmployeeId' => 'required',
            'selectedItems' => 'required|array|min:1',
            // La firma solo es requerida si NO es admin (si es técnico entregando)
            'signature' => auth()->user()->hasRole('Administrador') ? 'nullable' : 'required',
        ], [
            'selectedItems.required' => 'Debe seleccionar al menos un elemento.',
            'signature.required' => 'La firma es obligatoria.',
        ]);

        if (auth()->user()->hasRole('Administrador')) {
            // ADMIN: Crea una SOLICITUD
            \App\Models\EppDeliveryRequest::create([
                'empresa_id' => $this->empresa_id,
                'sede_id' => $this->sede_id,
                'employee_id' => $this->selectedEmployeeId,
                'requested_by_user_id' => auth()->id(),
                'status' => 'pending',
                'items' => $this->selectedItems,
                'notes' => $this->observaciones,
            ]);

            session()->flash('message', 'Solicitud de entrega registrada. El técnico la verá en sus tareas.');
            return redirect()->route('gestor.epp.history');

        } else {
            // TECNICO: Crea una ENTREGA REAL
            DB::transaction(function () {
                $delivery = EppDelivery::create([
                    'employee_id' => $this->selectedEmployeeId,
                    'user_id' => auth()->id(),
                    'delivery_date' => now(),
                    'signature' => $this->signature,
                    'observaciones' => $this->observaciones,
                ]);

                foreach ($this->selectedItems as $id => $data) {
                    $delivery->items()->attach($id, [
                        'cantidad' => $data['cantidad'],
                        'tipo_entrega' => $data['tipo_entrega'],
                    ]);
                }
            });

            // Si vino desde una solicitud, marcarla como completada
            $requestId = request()->query('request_id');
            if ($requestId) {
                $eppRequest = \App\Models\EppDeliveryRequest::find($requestId);
                if ($eppRequest) {
                    $eppRequest->update(['status' => 'completed']);
                }
            }

            session()->flash('message', 'Entrega de EPP registrada correctamente.');
            return redirect()->route('tecnico.epp.history');
        }
    }

    public function render()
    {
        $empresas = Empresa::orderBy('nombre')->get();
        $sedes = $this->empresa_id ? Sede::where('empresa_id', $this->empresa_id)->orderBy('nombre_sede')->get() : collect();
        
        $employees = collect();
        if ($this->sede_id) {
            $employees = Employee::where('sede_id', $this->sede_id)
                ->when($this->searchEmployee, fn($q) => $q->where('name', 'like', '%' . $this->searchEmployee . '%')->orWhere('last_name', 'like', '%' . $this->searchEmployee . '%'))
                ->get();
        }

        $eppItems = EppItem::where('is_active', true)->orderBy('tipo')->get();

        return view('livewire.epp.epp-delivery-wizard', [
            'empresas' => $empresas,
            'sedes' => $sedes,
            'employees' => $employees,
            'eppItems' => $eppItems,
            'selectedEmployee' => $this->selectedEmployeeId ? Employee::find($this->selectedEmployeeId) : null,
        ])->layout('components.layouts.app');
    }
}
