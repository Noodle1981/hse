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

    #[\Livewire\Attributes\Url]
    public $sede_id;
    #[\Livewire\Attributes\Url]
    public $task_id;
    #[\Livewire\Attributes\Url]
    public $type;
    #[\Livewire\Attributes\Url]
    public $source;

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
        // 1. Forzar captura de parámetros de la Request para telemetría
        $this->sede_id = $this->sede_id ?: request()->query('sede_id');
        $this->task_id = $this->task_id ?: request()->query('task_id');
        $this->type = $this->type ?: request()->query('type');
        $this->source = $this->source ?: request()->query('source');

        // 2. Comprobar Visita Activa (Prioridad 1)
        $activeVisit = $this->visitService->getActiveVisit(Auth::user());
        if ($activeVisit) {
            session()->flash('info', '🛰️ Reanudando visita en curso: ' . $activeVisit->sede->nombre_sede);
            return redirect()->route('tecnico.visits.in-progress', array_merge(['visit' => $activeVisit->id], array_filter([
                'task_id' => $this->task_id,
                'type' => $this->type,
                'source' => $this->source
            ])));
        }

        // 3. Inicio Contextual (Prioridad 2)
        if ($this->sede_id) {
            $sede = Sede::with('empresa')->find($this->sede_id);
            if ($sede) {
                session()->flash('message', '🚀 Lanzando visitador táctico para: ' . $sede->nombre_sede);
                $visit = $this->visitService->startVisit(Auth::user(), $sede);
                
                return redirect()->route('tecnico.visits.in-progress', [
                    'visit' => $visit->id,
                    'task_id' => $this->task_id,
                    'type' => $this->type,
                    'source' => $this->source
                ]);
            } else {
                session()->flash('error', '❌ Sede no encontrada en zona de despliegue: ID ' . $this->sede_id);
            }
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

        return redirect()->route('tecnico.visits.in-progress', $visit);
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
