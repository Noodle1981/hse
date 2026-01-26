<?php

namespace App\Livewire\Risks;

use App\Models\RiskMatrix;
use App\Models\RiskEvaluation;
use App\Models\Risk;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class RiskDashboard extends Component
{
    public $matrixId = null; // Opcional: filtrar por una matriz específica
    public $heatmapData = [];
    public $kpis = [
        'total_risks' => 0,
        'critical' => 0,
        'high' => 0,
        'medium' => 0,
        'low' => 0,
        'alert_triggers' => 0
    ];

    public function mount($matrixId = null)
    {
        $this->matrixId = $matrixId;
        $this->loadData();
    }

    public function loadData()
    {
        // 1. Inicializar heatmap 5x5 con ceros
        for ($s = 5; $s >= 1; $s--) {
            for ($p = 1; $p <= 5; $p++) {
                $this->heatmapData[$s][$p] = 0;
            }
        }

        // 2. Obtener las últimas evaluaciones
        // Una consulta para traer lo último de cada riesgo
        $subquery = DB::table('risk_evaluations')
            ->select('risk_id', DB::raw('MAX(id) as last_id'))
            ->groupBy('risk_id');

        $query = RiskEvaluation::query()
            ->joinSub($subquery, 'latest_evals', function ($join) {
                $join->on('risk_evaluations.id', '=', 'latest_evals.last_id');
            });

        $user = auth()->user();
        if ($user->rol === 'Técnico') {
            $query->whereHas('risk.matrix', function($q) use ($user) {
                $q->whereIn('sede_id', $user->sedes()->pluck('sedes.id'));
            });
        }

        if ($this->matrixId) {
            $query->whereHas('risk', function($q) {
                $q->where('risk_matrix_id', $this->matrixId);
            });
        }

        $latestEvaluations = $query->get();

        // 3. Poblar heatmap y KPIs
        $this->resetKpis();
        
        foreach ($latestEvaluations as $eval) {
            $this->heatmapData[$eval->severity][$eval->probability]++;
            
            $this->kpis['total_risks']++;
            if ($eval->level === 'Crítico') $this->kpis['critical']++;
            elseif ($eval->level === 'Alto') $this->kpis['high']++;
            elseif ($eval->level === 'Medio') $this->kpis['medium']++;
            else $this->kpis['low']++;
        }

        // 4. Calcular disparadores de alerta activos
        $this->kpis['alert_triggers'] = Risk::query()
            ->when($this->matrixId, fn($q) => $q->where('risk_matrix_id', $this->matrixId))
            ->when($user->rol === 'Técnico', function($q) use ($user) {
                $q->whereHas('matrix', fn($qm) => $qm->whereIn('sede_id', $user->sedes()->pluck('sedes.id')));
            })
            ->get()
            ->sum(fn($risk) => $risk->getRecentFailuresCount());
    }

    private function resetKpis()
    {
        $this->kpis = [
            'total_risks' => 0,
            'critical' => 0,
            'high' => 0,
            'medium' => 0,
            'low' => 0,
            'alert_triggers' => 0
        ];
    }

    public function getCellColor($s, $p)
    {
        $score = $s * $p;
        if ($score >= 17) return 'bg-red-500';
        if ($score >= 10) return 'bg-orange-500';
        if ($score >= 5) return 'bg-yellow-500';
        return 'bg-green-500';
    }

    public function updatedMatrixId()
    {
        $this->loadData();
    }

    public function render()
    {
        $user = auth()->user();
        
        $matrices = RiskMatrix::query()
            ->when($user->rol === 'Técnico', function($q) use ($user) {
                $q->whereIn('sede_id', $user->sedes()->pluck('sedes.id'));
            })
            ->get();

        return view('livewire.risks.risk-dashboard', [
            'matrices' => $matrices
        ])->layout('components.layouts.app');
    }
}
