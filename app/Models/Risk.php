<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Risk extends Model
{
    protected $fillable = [
        'risk_matrix_id',
        'hazard',
        'type',
        'consequence',
        'order',
    ];

    public function matrix()
    {
        return $this->belongsTo(RiskMatrix::class, 'risk_matrix_id');
    }

    public function evaluations()
    {
        return $this->hasMany(RiskEvaluation::class)->orderByDesc('evaluation_date');
    }

    public function latestEvaluation()
    {
        return $this->hasOne(RiskEvaluation::class)->latestOfMany('evaluation_date');
    }

    public function controls()
    {
        return $this->hasMany(RiskControl::class);
    }

    /**
     * Hallazgos asociados a este riesgo (OPCIONAL - Fase 2 Hallazgos)
     */
    public function hallazgos()
    {
        return $this->hasMany(Hallazgo::class);
    }

    /**
     * Preguntas de checklist vinculadas a este riesgo
     */
    public function checklistQuestions()
    {
        return $this->hasMany(DynamicQuestion::class, 'risk_id');
    }

    /**
     * Obtener los objetos DynamicAnswer que fallaron recientemente (últimos 30 días)
     */
    public function getRecentFailures()
    {
        $thirtyDaysAgo = now()->subDays(30);

        return DynamicAnswer::with(['question', 'inspection.sede'])
            ->whereHas('question', function ($q) {
                $q->where('risk_id', $this->id);
            })
            ->whereHas('inspection', function ($q) use ($thirtyDaysAgo) {
                $q->where('status', 'completed')
                  ->where('completed_at', '>=', $thirtyDaysAgo);
            })
            ->get()
            ->filter(fn($answer) => $answer->isFailure());
    }

    /**
     * Obtener el conteo de fallos recientes
     */
    public function getRecentFailuresCount(): int
    {
        return $this->getRecentFailures()->count();
    }

    /**
     * Obtener TODOS los objetos DynamicAnswer que han fallado históricamente
     */
    public function getAllHistoricalFailures()
    {
        return DynamicAnswer::with(['question', 'inspection.sede', 'inspection.checklist'])
            ->whereHas('question', function ($q) {
                $q->where('risk_id', $this->id);
            })
            ->whereHas('inspection', function ($q) {
                $q->where('status', 'completed');
            })
            ->orderByDesc('created_at')
            ->get()
            ->filter(fn($answer) => $answer->isFailure());
    }

    /**
     * Obtener capacitaciones relacionadas a este riesgo (a través de los checklists vinculados)
     */
    public function relatedTrainings()
    {
        $checklistIds = $this->checklistQuestions()->pluck('dynamic_checklist_id')->unique();
        
        return Training::whereIn('dynamic_checklist_id', $checklistIds)->get();
    }
}
