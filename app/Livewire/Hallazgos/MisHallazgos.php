<?php

namespace App\Livewire\Hallazgos;

use App\Models\Hallazgo;
use Livewire\Component;
use Livewire\WithPagination;

class MisHallazgos extends Component
{
    use WithPagination;

    public string $filterEstado = '';
    public string $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        $assignedSedeIds = $user->sedes()->pluck('sedes.id')->toArray();

        $query = Hallazgo::query()
            ->where(function ($q) use ($user, $assignedSedeIds) {
                $q->where('user_id', $user->id)
                  ->orWhereIn('sede_id', $assignedSedeIds);
            })
            ->with(['sede', 'seguimientos', 'fotos', 'incidentes'])
            ->withCount(['seguimientos', 'fotos', 'incidentes']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('titulo', 'like', '%' . $this->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterEstado) {
            $query->where('estado', $this->filterEstado);
        }

        $hallazgos = $query->latest()->paginate(10);

        // KPIs (incluyendo sedes asignadas)
        $kpis = [
            'total' => Hallazgo::where(function($q) use ($user, $assignedSedeIds) {
                $q->where('user_id', $user->id)->orWhereIn('sede_id', $assignedSedeIds);
            })->count(),
            'pendientes' => Hallazgo::where(function($q) use ($user, $assignedSedeIds) {
                $q->where('user_id', $user->id)->orWhereIn('sede_id', $assignedSedeIds);
            })->pendientes()->count(),
            'en_proceso' => Hallazgo::where(function($q) use ($user, $assignedSedeIds) {
                $q->where('user_id', $user->id)->orWhereIn('sede_id', $assignedSedeIds);
            })->enProceso()->count(),
            'resueltos' => Hallazgo::where(function($q) use ($user, $assignedSedeIds) {
                $q->where('user_id', $user->id)->orWhereIn('sede_id', $assignedSedeIds);
            })->resueltos()->count(),
            'criticos_abiertos' => Hallazgo::where(function($q) use ($user, $assignedSedeIds) {
                $q->where('user_id', $user->id)->orWhereIn('sede_id', $assignedSedeIds);
            })->criticos()->abiertos()->count(),
        ];

        return view('livewire.hallazgos.mis-hallazgos', [
            'hallazgos' => $hallazgos,
            'kpis' => $kpis,
        ])->layout('components.layouts.app');
    }
}
