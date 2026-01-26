<?php

namespace App\Livewire\Risks;

use App\Models\RiskMatrix;
use App\Models\Risk;
use App\Models\RiskControl;
use App\Models\DynamicChecklist;
use Livewire\Component;

class RiskMatrixEditor extends Component
{
    public $matrixId;
    public $matrix;
    
    // Risk Form
    public $showRiskModal = false;
    public $editingRiskId = null;
    public $hazard, $type, $consequence;
    
    // Control Form
    public $showControlModal = false;
    public $currentRiskId = null;
    public $editingControlId = null;
    public $measure, $controlType = 'Administrativo', $checklist_id;
    
    // History Modal
    public $showHistoryModal = false;
    public $historyFailures = [];
    public $historyRisk = null;

    public function mount($matrixId)
    {
        $this->matrixId = $matrixId;
        $this->loadMatrix();
    }

    public function loadMatrix()
    {
        $this->matrix = RiskMatrix::with(['risks.controls', 'empresa', 'sede'])->findOrFail($this->matrixId);
    }

    // Risk Management
    public function addRisk()
    {
        $this->resetRiskForm();
        $this->showRiskModal = true;
    }

    public function editRisk($id)
    {
        $risk = Risk::findOrFail($id);
        $this->editingRiskId = $risk->id;
        $this->hazard = $risk->hazard;
        $this->type = $risk->type;
        $this->consequence = $risk->consequence;
        $this->showRiskModal = true;
    }

    public function saveRisk()
    {
        $this->validate([
            'hazard' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'consequence' => 'nullable|string',
        ]);

        if ($this->editingRiskId) {
            Risk::find($this->editingRiskId)->update([
                'hazard' => $this->hazard,
                'type' => $this->type,
                'consequence' => $this->consequence,
            ]);
        } else {
            $this->matrix->risks()->create([
                'hazard' => $this->hazard,
                'type' => $this->type,
                'consequence' => $this->consequence,
                'order' => $this->matrix->risks()->count(),
            ]);
        }

        $this->showRiskModal = false;
        $this->loadMatrix();
    }

    public function deleteRisk($id)
    {
        Risk::find($id)->delete();
        $this->loadMatrix();
    }

    // Control Management
    public function addControl($riskId)
    {
        $this->resetControlForm();
        $this->currentRiskId = $riskId;
        $this->showControlModal = true;
    }

    public function editControl($id)
    {
        $control = RiskControl::findOrFail($id);
        $this->editingControlId = $control->id;
        $this->currentRiskId = $control->risk_id;
        $this->measure = $control->measure;
        $this->controlType = $control->type;
        $this->checklist_id = $control->checklist_id;
        $this->showControlModal = true;
    }

    public function saveControl()
    {
        $this->validate([
            'measure' => 'required|string|max:255',
            'controlType' => 'required|in:Eliminación,Sustitución,Ingeniería,Administrativo,EPP',
            'checklist_id' => 'nullable|exists:dynamic_checklists,id',
        ]);

        if ($this->editingControlId) {
            RiskControl::find($this->editingControlId)->update([
                'measure' => $this->measure,
                'type' => $this->controlType,
                'checklist_id' => $this->checklist_id,
            ]);
        } else {
            RiskControl::create([
                'risk_id' => $this->currentRiskId,
                'measure' => $this->measure,
                'type' => $this->controlType,
                'checklist_id' => $this->checklist_id,
            ]);
        }

        $this->showControlModal = false;
        $this->loadMatrix();
    }

    public function deleteControl($id)
    {
        RiskControl::find($id)->delete();
        $this->loadMatrix();
    }

    public function openHistory($riskId)
    {
        $this->historyRisk = Risk::findOrFail($riskId);
        $this->historyFailures = $this->historyRisk->getAllHistoricalFailures();
        $this->showHistoryModal = true;
    }

    public function closeHistory()
    {
        $this->showHistoryModal = false;
        $this->historyRisk = null;
        $this->historyFailures = [];
    }

    private function resetRiskForm()
    {
        $this->editingRiskId = null;
        $this->hazard = '';
        $this->type = '';
        $this->consequence = '';
    }

    private function resetControlForm()
    {
        $this->editingControlId = null;
        $this->measure = '';
        $this->controlType = 'Administrativo';
        $this->checklist_id = null;
    }

    public function render()
    {
        return view('livewire.risks.risk-matrix-editor', [
            'checklists' => DynamicChecklist::active()->get(),
        ])->layout('components.layouts.app');
    }
}
