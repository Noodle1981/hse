<?php

namespace App\Livewire\Visits;

use App\Models\Employee;
use App\Models\EppItem;
use App\Models\VisitCertificate;
use App\Services\VisitService;
use Livewire\Component;

class EppDelivery extends Component
{
    public VisitCertificate $visit;
    public $showModal = false;

    // Data Sources
    public $employees = [];
    public $eppItemsCatalog = [];

    // Form
    public $employeeId;
    public $requestId; // Nuevo: rastrear si viene de una solicitud
    public $selectedItems = []; // [{item_id, name, qty, size, reason}]
    public $notes;

    // Temporary item add
    public $newItemId;
    public $newItemQty = 1;
    public $newItemSize;
    public $newItemReason = 'Entrega'; // Entrega, Recambio

    protected $visitService;

    public function boot(VisitService $visitService)
    {
        $this->visitService = $visitService;
    }

    public function mount(VisitCertificate $visit, $requestId = null)
    {
        $this->visit = $visit;
        $this->loadData();
        
        $this->requestId = $requestId;
        if ($requestId) {
            $eppRequest = \App\Models\EppDeliveryRequest::find($requestId);
            if ($eppRequest) {
                // Pre-fill form
                $this->employeeId = $eppRequest->employee_id;
                $this->notes = $eppRequest->notes;
                
                // Convert request items to selectedItems format
                // request items: {item_id: {cantidad, tipo_entrega}}
                // selectedItems: [{item_id, name, qty, size, reason}]
                foreach ($eppRequest->items as $itemId => $itemData) {
                    $eppItem = EppItem::find($itemId);
                    if ($eppItem) {
                        $this->selectedItems[] = [
                            'item_id' => $eppItem->id,
                            'name' => $eppItem->nombre,
                            'qty' => is_array($itemData) ? ($itemData['cantidad'] ?? 1) : 1,
                            'size' => null,
                            'reason' => 'Entrega',
                        ];
                    }
                }
            }
        }
    }

    public function loadData()
    {
        // Cargar empleados de la sede o empresa
        $this->employees = Employee::where('empresa_id', $this->visit->empresa_id)
            ->where('is_active', true)
            // ->where('sede_id', $this->visit->sede_id) // Opcional: filtrar solo por sede
            ->orderBy('last_name')
            ->get();

        // Cargar catálogo de EPPs activos
        $this->eppItemsCatalog = EppItem::where('is_active', true)
            ->orderBy('nombre')
            ->get();
    }

    public function addItem()
    {
        $this->validate([
            'newItemId' => 'required|exists:epp_items,id',
            'newItemQty' => 'required|integer|min:1',
        ]);

        $item = EppItem::find($this->newItemId);

        $this->selectedItems[] = [
            'item_id' => $item->id,
            'name' => $item->nombre,
            'qty' => $this->newItemQty,
            'size' => $this->newItemSize,
            'reason' => $this->newItemReason,
        ];

        // Reset inputs
        $this->newItemId = null;
        $this->newItemQty = 1;
        $this->newItemSize = null;
        $this->newItemReason = 'Entrega';
    }

    public function removeItem($index)
    {
        unset($this->selectedItems[$index]);
        $this->selectedItems = array_values($this->selectedItems);
    }

    public function save()
    {
        $this->validate([
            'employeeId' => 'required|exists:employees,id',
            'selectedItems' => 'required|array|min:1',
        ]);

        $this->visitService->deliverEpp(
            $this->visit,
            $this->employeeId,
            $this->selectedItems,
            null, // signature
            $this->notes,
            $this->requestId ? true : false // Omitir decremento si ya se hizo en la solicitud
        );

        $this->dispatch('eppDelivered');
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->employeeId = null;
        $this->selectedItems = [];
        $this->notes = null;
        $this->newItemId = null;
    }

    public function close()
    {
        $this->dispatch('closeEppDelivery');
    }

    public function render()
    {
        return view('livewire.visits.epp-delivery');
    }
}
