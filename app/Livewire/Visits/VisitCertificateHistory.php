<?php

namespace App\Livewire\Visits;

use App\Models\GeneradorConstancia;
use App\Models\Empresa;
use App\Models\Sede;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use App\Mail\ConstanciaFirmaResponsable;

#[Layout('components.layouts.app')]
class VisitCertificateHistory extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $empresaFilter = '';
    public $sedeFilter = '';
    public $fromDate;
    public $toDate;

    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingEmpresaFilter() { $this->sedeFilter = ''; $this->resetPage(); }
    public function updatingSedeFilter() { $this->resetPage(); }

    public function resendEmail($id)
    {
        $certificate = GeneradorConstancia::find($id);
        
        if ($certificate && !$certificate->firma_responsable && $certificate->email_responsable) {
            try {
                Mail::to($certificate->email_responsable)->send(new ConstanciaFirmaResponsable($certificate));
                session()->flash('success', 'Email reenviado correctamente a ' . $certificate->email_responsable);
            } catch (\Exception $e) {
                session()->flash('error', 'Error al reenviar email: ' . $e->getMessage());
            }
        } else {
             session()->flash('error', 'No se puede reenviar el email (ya firmado o sin destinatario).');
        }
    }

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $certificates = GeneradorConstancia::query()
            ->with(['sede.empresa'])
            ->where('user_id', $user->id)
            ->when($this->search, function ($q) {
                $q->where(function($sub) {
                    $sub->whereHas('sede', function ($sq) {
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
            ->when($this->empresaFilter, function ($q) {
                $q->whereHas('sede', function ($sq) {
                    $sq->where('empresa_id', $this->empresaFilter);
                });
            })
            ->when($this->sedeFilter, function ($q) {
                $q->where('sede_id', $this->sedeFilter);
            })
            ->when($this->fromDate, function ($q) {
                $q->whereDate('created_at', '>=', $this->fromDate);
            })
            ->when($this->toDate, function ($q) {
                $q->whereDate('created_at', '<=', $this->toDate);
            })
            ->latest()
            ->paginate(15);

        $empresas = Empresa::whereHas('sedes.constancias', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderBy('nombre')
            ->get();

        $sedes = $this->empresaFilter 
            ? Sede::where('empresa_id', $this->empresaFilter)
                ->whereHas('constancias', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->orderBy('nombre_sede')
                ->get() 
            : collect();

        return view('livewire.visits.visit-certificate-history', [
            'certificates' => $certificates,
            'empresas' => $empresas,
            'sedes' => $sedes,
        ]);
    }
}
