<?php

namespace App\Livewire\Visits;

use App\Models\Sede;
use App\Models\VisitCertificate;
use App\Services\VisitService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class VisitManager extends Component
{
    use WithPagination;

    // Estado para iniciar visita
    public $showStartVisitModal = false;
    public $selectedSedeId;

    // Colecciones para selección
    public $sedes = [];

    // Dependencia
    protected $visitService;

    public function boot(VisitService $visitService)
    {
        $this->visitService = $visitService;
    }

    public function mount()
    {
        // Redirigir si ya hay visita en progreso
        $activeVisit = $this->visitService->getActiveVisit(Auth::user());
        if ($activeVisit) {
            return redirect()->route('visits.in-progress', $activeVisit);
        }
    }

    public function openStartVisitModal()
    {
        // Cargar sedes disponibles (podría filtrar por empresa o asignación)
        // Por ahora cargamos todas las sedes activas
        // En futuro: filtrar por asignaciones pendientes
        $this->sedes = Sede::query()
            ->with('empresa')
            ->orderBy('nombre_sede')
            ->get();
            
        $this->showStartVisitModal = true;
    }

    public function startVisit()
    {
        $this->validate([
            'selectedSedeId' => 'required|exists:sedes,id',
        ]);

        $sede = Sede::find($this->selectedSedeId);
        
        $visit = $this->visitService->startVisit(Auth::user(), $sede);

        return redirect()->route('visits.in-progress', $visit);
    }

    public function render()
    {
        $pastVisits = VisitCertificate::where('user_id', Auth::id())
            ->whereIn('status', ['completed', 'signed'])
            ->latest('visit_date')
            ->paginate(10);

        return view('livewire.visits.visit-manager', [
            'pastVisits' => $pastVisits
        ]);
    }
}
