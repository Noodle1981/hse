<?php

namespace App\Livewire\Trainings;

use Livewire\Component;

use App\Models\Training;
use App\Models\Empresa;
use App\Models\Sede;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class TrainingIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $empresaFilter = '';
    public $sedeFilter = '';
    public $viewMode = 'templates'; // 'templates' | 'executions'

    protected $queryString = [
        'search' => ['except' => ''],
        'empresaFilter' => ['as' => 'empresa', 'except' => ''],
        'sedeFilter' => ['as' => 'sede', 'except' => ''],
        'viewMode' => ['except' => 'templates'],
    ];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingEmpresaFilter() { $this->resetPage(); $this->sedeFilter = ''; }
    public function updatingSedeFilter() { $this->resetPage(); }

    public function delete($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // Validar que solo Admin puede eliminar
        if (!$user->isAdmin()) {
            session()->flash('error', 'No tienes permisos para eliminar capacitaciones.');
            return;
        }

        $training = Training::findOrFail($id);
        
        // Validar que no tenga asistencias registradas
        if ($training->attendances()->count() > 0) {
            session()->flash('error', 'No se puede eliminar una capacitación con asistencias registradas.');
            return;
        }
        
        $training->delete();
        session()->flash('message', 'Capacitación eliminada correctamente.');
    }

    public function render()
    {
        $month = strtolower(now()->format('F'));
        $trainings = collect();
        
        if ($this->viewMode === 'templates') {
            // 1. Manual Trainings (Solicitados)
            $manualQuery = Training::query()
                ->with(['empresa', 'sede', 'responsibleUser'])
                ->withCount('attendances')
                ->when($this->search, fn($q) => $q->where('title', 'like', '%' . $this->search . '%'))
                ->when($this->empresaFilter, fn($q) => $q->where('empresa_id', $this->empresaFilter))
                ->when($this->sedeFilter, fn($q) => $q->where('sede_id', $this->sedeFilter))
                ->latest();

            // 2. Contractual Trainings (Plan Anual)
            $contractQuery = \App\Models\AnnualPlan::with(['sede', 'sede.empresa'])
                ->where('category', 'Capacitación')
                ->where($month, true)
                ->where('year', now()->year)
                ->when($this->search, function ($q) {
                    $q->where('activity_name', 'like', '%' . $this->search . '%');
                })
                ->when($this->empresaFilter, function ($q) {
                    $q->whereHas('sede', fn($s) => $s->where('empresa_id', $this->empresaFilter));
                })
                ->when($this->sedeFilter, fn($q) => $q->where('sede_id', $this->sedeFilter));

            // Mapping & Merging
            $manualResults = $manualQuery->get()->map(fn($t) => (object)[
                'id' => $t->id,
                'title' => $t->title,
                'trainer_name' => $t->trainer_name,
                'responsibleUser' => $t->responsibleUser,
                'empresa' => $t->empresa,
                'sede' => $t->sede,
                'date' => $t->date,
                'status' => $t->status,
                'attendances_count' => $t->attendances_count,
                'source' => 'manual',
                'duration' => $t->duration . ' min',
                'type' => $t->type,
                'registry_code' => 'CAP-' . $t->id
            ]);

            $contractResults = $contractQuery->get()->map(fn($p) => (object)[
                'id' => null,
                'title' => $p->activity_name,
                'trainer_name' => 'Operadores de Sede',
                'responsibleUser' => null,
                'empresa' => $p->sede?->empresa,
                'sede' => $p->sede,
                'date' => now()->endOfMonth(),
                'status' => 'scheduled',
                'attendances_count' => 0,
                'view_attendances_count' => null,
                'source' => 'contract',
                'duration' => '60 min',
                'type' => 'Presencial',
                'registry_code' => $p->activity_code
            ]);

            $allTrainings = $manualResults->concat($contractResults)->sortByDesc('date');
            
            // Manual Pagination
            $currentPage = $this->getPage();
            $perPage = 10;
            $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $allTrainings->forPage($currentPage, $perPage),
                $allTrainings->count(),
                $perPage,
                $currentPage,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );

            $trainings = $paginated;

        } else {
            // Modo executions (VisitTraining - Mapeo Estándar)
            $execQuery = \App\Models\VisitTraining::query()
                ->with(['visit.empresa', 'visit.sede', 'visit.user'])
                ->when($this->search, fn($q) => $q->where('title', 'like', '%' . $this->search . '%'))
                ->when($this->empresaFilter, function($q) {
                    $q->whereHas('visit', fn($v) => $v->where('empresa_id', $this->empresaFilter));
                })
                ->when($this->sedeFilter, function($q) {
                    $q->whereHas('visit', fn($v) => $v->where('sede_id', $this->sedeFilter));
                })
                ->latest();

            $execResults = $execQuery->paginate(10);
            
            // Transformar la colección paginada manteniendo la estructura de paginación
            $transformed = $execResults->getCollection()->map(fn($vt) => (object)[
                'id' => $vt->id,
                'title' => $vt->title,
                'trainer_name' => $vt->visit?->user?->name ?? 'Técnico Externo',
                'responsibleUser' => $vt->visit?->user,
                'empresa' => $vt->visit?->empresa,
                'sede' => $vt->visit?->sede,
                'date' => $vt->created_at,
                'status' => 'completed',
                'attendances_count' => $vt->attendees_count,
                'source' => 'execution',
                'duration' => $vt->duration_minutes . ' min',
                'type' => 'Presencial (Visita)',
                'registry_code' => 'EXEC-' . $vt->id
            ]);

            $trainings = $execResults->setCollection($transformed);
        }

        $empresas = Empresa::orderBy('nombre')->get();
        $sedes = $this->empresaFilter 
            ? Sede::where('empresa_id', $this->empresaFilter)->orderBy('nombre_sede')->get() 
            : collect();

        return view('livewire.trainings.training-index', [
            'trainings' => $trainings,
            'empresas' => $empresas,
            'sedes' => $sedes,
        ]);
    }
}
