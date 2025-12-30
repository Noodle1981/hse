<?php

namespace App\Livewire\Visits;

use App\Models\GeneradorConstancia;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class VisitCertificateManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $technicianFilter = '';
    public $empresaFilter = '';
    public $sedeFilter = '';
    public $tipoVisitaFilter = '';
    public $fromDate;
    public $toDate;

    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingTechnicianFilter() { $this->resetPage(); }
    public function updatingEmpresaFilter() { $this->sedeFilter = ''; $this->resetPage(); }
    public function updatingSedeFilter() { $this->resetPage(); }
    public function updatingTipoVisitaFilter() { $this->resetPage(); }

    public function render()
    {
        $certificates = GeneradorConstancia::query()
            ->with(['sede.empresa', 'user'])
            ->when($this->search, function ($q) {
                $q->where(function($sub) {
                    $sub->whereHas('user', function ($sq) {
                        $sq->where('nombres', 'like', '%' . $this->search . '%')
                          ->orWhere('apellidos', 'like', '%' . $this->search . '%');
                    })->orWhereHas('sede', function ($sq) {
                        $sq->where('nombre_sede', 'like', '%' . $this->search . '%');
                    })->orWhere('tipo_visita', 'like', '%' . $this->search . '%')
                      ->orWhere('observacion', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter === 'pending', function ($q) {
                $q->whereNull('firma_responsable');
            })
            ->when($this->statusFilter === 'completed', function ($q) {
                $q->whereNotNull('firma_responsable');
            })
            ->when($this->technicianFilter, function ($q) {
                $q->where('user_id', $this->technicianFilter);
            })
            ->when($this->empresaFilter, function ($q) {
                $q->whereHas('sede', function ($sq) {
                    $sq->where('empresa_id', $this->empresaFilter);
                });
            })
            ->when($this->sedeFilter, function ($q) {
                $q->where('sede_id', $this->sedeFilter);
            })
            ->when($this->tipoVisitaFilter, function ($q) {
                $q->where('tipo_visita', $this->tipoVisitaFilter);
            })
            ->when($this->fromDate, function ($q) {
                $q->whereDate('created_at', '>=', $this->fromDate);
            })
            ->when($this->toDate, function ($q) {
                $q->whereDate('created_at', '<=', $this->toDate);
            })
            ->latest()
            ->paginate(15);

        $technicians = User::where('rol', 'Técnico')
            ->orderBy('nombres')
            ->get();

        $empresas = Empresa::whereHas('sedes.constancias')
            ->orderBy('nombre')
            ->get();

        $sedes = $this->empresaFilter 
            ? Sede::where('empresa_id', $this->empresaFilter)
                ->whereHas('constancias')
                ->orderBy('nombre_sede')
                ->get() 
            : collect();

        $tiposVisita = GeneradorConstancia::select('tipo_visita')
            ->distinct()
            ->orderBy('tipo_visita')
            ->pluck('tipo_visita');

        return view('livewire.visits.visit-certificate-manager', [
            'certificates' => $certificates,
            'technicians' => $technicians,
            'empresas' => $empresas,
            'sedes' => $sedes,
            'tiposVisita' => $tiposVisita,
        ])->layout('components.layouts.app');
    }
}
