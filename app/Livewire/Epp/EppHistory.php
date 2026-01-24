<?php

namespace App\Livewire\Epp;

use App\Models\EppDelivery;
use App\Models\VisitEppDelivery;
use App\Models\Empresa;
use App\Models\Sede;
use Livewire\Component;
use Livewire\WithPagination;

class EppHistory extends Component
{
    use WithPagination;

    public $search = '';
    public $empresaFilter = '';
    public $sedeFilter = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingEmpresaFilter() { $this->sedeFilter = ''; $this->resetPage(); }
    public function updatingSedeFilter() { $this->resetPage(); }

    public function render()
    {
        // Query legacy standalone deliveries
        $legacyDeliveries = EppDelivery::query()
            ->with(['employee', 'items', 'user', 'employee.empresa', 'employee.sede'])
            ->when(!auth()->user()->isAdmin(), function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->when($this->search, function ($q) {
                $q->whereHas('employee', function ($sq) {
                    $sq->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('last_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->empresaFilter, function ($q) {
                $q->whereHas('employee', fn($sq) => $sq->where('empresa_id', $this->empresaFilter));
            })
            ->when($this->sedeFilter, function ($q) {
                $q->whereHas('employee', fn($sq) => $sq->where('sede_id', $this->sedeFilter));
            })
            ->latest('delivery_date')
            ->get();

        // Query visit-based deliveries
        $visitDeliveries = VisitEppDelivery::query()
            ->with(['employee', 'visit.user', 'employee.empresa', 'employee.sede', 'visit'])
            ->when(!auth()->user()->isAdmin(), function ($q) {
                $q->whereHas('visit', fn($v) => $v->where('user_id', auth()->id()));
            })
            ->when($this->search, function ($q) {
                $q->whereHas('employee', function ($sq) {
                    $sq->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('last_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->empresaFilter, function ($q) {
                $q->whereHas('employee', fn($sq) => $sq->where('empresa_id', $this->empresaFilter));
            })
            ->when($this->sedeFilter, function ($q) {
                $q->whereHas('employee', fn($sq) => $sq->where('sede_id', $this->sedeFilter));
            })
            ->latest('created_at')
            ->get();

        // Merge and paginate manually
        $allDeliveries = $legacyDeliveries->concat($visitDeliveries)
            ->sortByDesc(function($delivery) {
                return $delivery instanceof VisitEppDelivery 
                    ? $delivery->created_at 
                    : $delivery->delivery_date;
            })
            ->values();

        // Manual pagination
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $perPage = 15;
        $currentPageItems = $allDeliveries->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $deliveries = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $allDeliveries->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        $empresas = Empresa::orderBy('nombre')->get();
        $sedes = $this->empresaFilter 
            ? Sede::where('empresa_id', $this->empresaFilter)->orderBy('nombre_sede')->get() 
            : collect();

        return view('livewire.epp.epp-history', [
            'deliveries' => $deliveries,
            'empresas' => $empresas,
            'sedes' => $sedes,
        ])->layout('components.layouts.app');
    }
}
