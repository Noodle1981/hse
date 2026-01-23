<?php

namespace App\Livewire\Visits;

use App\Models\VisitCertificate;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConstanciaFirmaResponsable;

class VisitHistory extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $filterTechnician = '';
    public $filterSede = '';

    public function resendEmail($id)
    {
        $visit = VisitCertificate::find($id);

        if ($visit && !$visit->responsible_signature && $visit->signature_skip_reason && $visit->email_responsable) {
            try {
                Mail::to($visit->email_responsable)->send(new ConstanciaFirmaResponsable($visit));
                session()->flash('message', 'Email enviado correctamente a ' . $visit->email_responsable);
            } catch (\Exception $e) {
                session()->flash('error', 'Error enviando email: ' . $e->getMessage());
            }
        }
    }

    public function render()
    {
        // Gestor ve TODAS las visitas del sistema
        $visits = VisitCertificate::query()
            ->with(['user', 'sede.empresa'])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->whereHas('sede', function($sq) {
                        $sq->where('nombre_sede', 'like', "%{$this->search}%");
                    })
                    ->orWhereHas('sede.empresa', function($eq) {
                        $eq->where('nombre', 'like', "%{$this->search}%");
                    })
                    ->orWhereHas('user', function($uq) {
                        $uq->where('name', 'like', "%{$this->search}%");
                    });
                });
            })
            ->when($this->filterStatus, function($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterTechnician, function($query) {
                $query->where('user_id', $this->filterTechnician);
            })
            ->when($this->filterSede, function($query) {
                $query->where('sede_id', $this->filterSede);
            })
            ->latest('visit_date')
            ->paginate(15);

        return view('livewire.visits.visit-history', [
            'visits' => $visits
        ])->layout('components.layouts.app');
    }
}
