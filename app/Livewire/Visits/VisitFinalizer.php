<?php

namespace App\Livewire\Visits;

use App\Models\VisitCertificate;
use App\Services\VisitService;
use Livewire\Component;

class VisitFinalizer extends Component
{
    public VisitCertificate $visit;
    public $generalObservations;
    public $signature; // Texto simple simulando firma por ahora

    // Resumen para visualización
    public $checklistsCount = 0;
    public $trainingsCount = 0;

    protected $visitService;

    public function boot(VisitService $visitService)
    {
        $this->visitService = $visitService;
    }

    public function mount(VisitCertificate $visit)
    {
        $this->visit = $visit;
        
        // Cargar resumen
        $this->checklistsCount = $visit->checklistCompletions()->count();
        $this->trainingsCount = $visit->trainings()->count();
    }

    protected $rules = [
        'generalObservations' => 'nullable|string|max:2000',
        'signature' => 'required|string|min:3', // Simulación de firma requerida
    ];

    public function finalize()
    {
        $this->validate();

        $this->visitService->finalizeVisit(
            $this->visit,
            $this->generalObservations,
            $this->signature
        );

        // Redirigir al manager o mostrar éxito
        if (auth()->user()->hasRole('Administrador')) {
            return redirect()->route('gestor.visits.history');
        }
        return redirect()->route('tecnico.visits.index');
    }

    public function cancel()
    {
        // Emitir evento al padre para cerrar modal
        $this->dispatch('closeFinalizeModal'); 
        // Nota: En VisitInProgress necesito escuchar este evento o usar $parent si es anidado simple
        // Para simplificar, en Blade usaré $set('showFinalizeModal', false) directamente si es posible,
        // pero como es componente aislado, emitir evento es mejor.
    }

    public function render()
    {
        return view('livewire.visits.visit-finalizer');
    }
}
