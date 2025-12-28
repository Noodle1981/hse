<?php

namespace App\Livewire\Risks;

use App\Models\RiskMatrix;
use App\Models\Risk;
use App\Models\RiskEvaluation;
use Livewire\Component;

class RiskEvaluator extends Component
{
    public $matrixId;
    public $matrix;
    
    // Evaluation state
    public $evaluations = []; // [risk_id => ['probability' => X, 'severity' => Y, 'justification' => Z]]
    public $showSuccess = false;

    public function mount($matrixId)
    {
        $this->matrixId = $matrixId;
        $this->loadMatrix();
        $this->initializeEvaluations();
    }

    public function loadMatrix()
    {
        $this->matrix = RiskMatrix::with(['risks.latestEvaluation', 'risks.controls', 'empresa', 'sede'])
            ->findOrFail($this->matrixId);
    }

    public function initializeEvaluations()
    {
        foreach ($this->matrix->risks as $risk) {
            $latest = $risk->latestEvaluation;
            $this->evaluations[$risk->id] = [
                'probability' => $latest ? $latest->probability : 1,
                'severity' => $latest ? $latest->severity : 1,
                'justification' => $latest ? $latest->justification : '',
                'score' => $latest ? $latest->score : 1,
                'level' => $latest ? $latest->level : 'Bajo',
                'color' => $latest ? $latest->color : '#10B981', // emerald-500
            ];
            
            if (!$latest) {
                $this->updateCalculation($risk->id);
            }
        }
    }

    public function updateCalculation($riskId)
    {
        $p = (int)$this->evaluations[$riskId]['probability'];
        $s = (int)$this->evaluations[$riskId]['severity'];
        $score = $p * $s;
        
        $level = 'Bajo';
        $color = '#10B981'; // emerald-500

        if ($score >= 17) {
            $level = 'Crítico';
            $color = '#EF4444'; // red-500
        } elseif ($score >= 10) {
            $level = 'Alto';
            $color = '#F59E0B'; // amber-500
        } elseif ($score >= 5) {
            $level = 'Medio';
            $color = '#EAB308'; // yellow-500
        }

        $this->evaluations[$riskId]['score'] = $score;
        $this->evaluations[$riskId]['level'] = $level;
        $this->evaluations[$riskId]['color'] = $color;
    }

    public function saveEvaluation($riskId)
    {
        $data = $this->evaluations[$riskId];
        
        RiskEvaluation::create([
            'risk_id' => $riskId,
            'user_id' => auth()->id(),
            'probability' => $data['probability'],
            'severity' => $data['severity'],
            'score' => $data['score'],
            'level' => $data['level'],
            'color' => $data['color'],
            'justification' => $data['justification'],
            'evaluation_date' => now(),
        ]);

        $this->showSuccess = true;
        $this->loadMatrix();
        
        session()->flash("risk_{$riskId}", "Evaluación guardada.");
    }

    public function render()
    {
        return view('livewire.risks.risk-evaluator')
            ->layout('components.layouts.app');
    }
}
