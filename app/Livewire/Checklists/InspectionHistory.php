<?php

namespace App\Livewire\Checklists;

use App\Models\DynamicInspection;
use Livewire\Component;
use Livewire\WithPagination;

class InspectionHistory extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $resultFilter = '';
    public ?int $checklistFilter = null;
    public ?int $empresaFilter = null;
    public ?int $sedeFilter = null;
    public $fromDate;
    public $toDate;

    protected $queryString = ['search', 'statusFilter', 'resultFilter', 'checklistFilter', 'empresaFilter', 'sedeFilter', 'fromDate', 'toDate'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedEmpresaFilter()
    {
        $this->sedeFilter = null;
        $this->resetPage();
    }

    public function updatedSedeFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        $isTech = $user->isTechnician();

        $inspections = DynamicInspection::query()
            ->with(['checklist', 'user', 'sede.empresa'])
            ->when($isTech, function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->when($this->search, function ($query) {
                $query->where(function($sub) {
                    $sub->whereHas('checklist', function ($q) {
                        $q->where('title', 'like', '%' . $this->search . '%');
                    })->orWhereHas('user', function ($q) {
                        $q->where('nombres', 'like', '%' . $this->search . '%')
                          ->orWhere('apellidos', 'like', '%' . $this->search . '%');
                    })->orWhereHas('sede', function ($q) {
                        $q->where('nombre_sede', 'like', '%' . $this->search . '%');
                    });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->resultFilter, function ($query) {
                $query->where('result', $this->resultFilter);
            })
            ->when($this->checklistFilter, function ($query) {
                $query->where('dynamic_checklist_id', $this->checklistFilter);
            })
            ->when($this->empresaFilter, function ($query) {
                $query->whereHas('sede', function ($q) {
                    $q->where('empresa_id', $this->empresaFilter);
                });
            })
            ->when($this->sedeFilter, function ($query) {
                $query->where('sede_id', $this->sedeFilter);
            })
            ->when($this->fromDate, function ($query) {
                $query->whereDate('completed_at', '>=', $this->fromDate);
            })
            ->when($this->toDate, function ($query) {
                $query->whereDate('completed_at', '<=', $this->toDate);
            })
            ->latest()
            ->paginate(15);

        return view('livewire.checklists.inspection-history', [
            'inspections' => $inspections,
            'empresas' => \App\Models\Empresa::whereHas('sedes.inspections')
                ->orderBy('nombre')->get(),
            'sedes' => $this->empresaFilter 
                ? \App\Models\Sede::where('empresa_id', $this->empresaFilter)
                    ->whereHas('inspections')
                    ->orderBy('nombre_sede')->get() 
                : collect(),
            'checklists' => \App\Models\DynamicChecklist::whereHas('inspections')
                ->orderBy('title')->get(),
        ])->layout('components.layouts.app');
    }
    public function delete($id)
    {
        $inspection = DynamicInspection::findOrFail($id);
        
        // Solo permitir borrar borradores o si es admin (adaptar según política)
        if ($inspection->status !== 'draft' && !auth()->user()->isAdmin()) {
            return;
        }

        $inspection->delete();
        $this->dispatch('inspection-deleted'); // Opcional, para notificaciones
    }
}
