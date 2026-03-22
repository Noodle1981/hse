<?php

namespace App\Livewire\MasterReports;

use Livewire\Component;

class MasterReportIndex extends Component
{
    public $search = '';
    public $status = '';

    // Solicitud de Revisión
    public $observacion_revision = '';
    public $reporteIdParaRevisar = null;
    public $showRevisarModal = false;

    public function prepararRevision($id)
    {
        $this->reporteIdParaRevisar = $id;
        $this->observacion_revision = '';
        $this->showRevisarModal = true;
    }

    public function enviarRevision()
    {
        $this->validate([
            'observacion_revision' => 'required|string|min:5|max:1000',
        ], [
            'observacion_revision.required' => 'La observación es obligatoria para solicitar correcciones.',
        ]);

        $report = \App\Models\MasterReport::findOrFail($this->reporteIdParaRevisar);
        
        $report->update([
            'estado' => 'en_redaccion',
            'observacion_revision' => $this->observacion_revision,
        ]);

        $this->showRevisarModal = false;
        $this->observacion_revision = '';
        $this->reporteIdParaRevisar = null;

        session()->flash('success', 'Solicitud de corrección enviada al Técnico.');
    }

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
