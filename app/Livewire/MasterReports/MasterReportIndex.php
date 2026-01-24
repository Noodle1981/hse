<?php

namespace App\Livewire\MasterReports;

use Livewire\Component;

class MasterReportIndex extends Component
{
    public $search = '';
    public $status = '';

    public function render()
    {
        $query = \App\Models\MasterReport::with(['sede', 'tecnico', 'gestor'])
            ->latest();

        if (auth()->user()->isTechnician()) {
            $query->where('user_id', auth()->id());
        }

        if ($this->status) {
            $query->where('estado', $this->status);
        }

        if ($this->search) {
            $query->whereHas('sede', function($q) {
                $q->where('nombre_sede', 'like', '%' . $this->search . '%');
            })->orWhere('titulo', 'like', '%' . $this->search . '%');
        }

        return view('livewire.master-reports.master-report-index', [
            'reports' => $query->paginate(10)
        ]);
    }
}
