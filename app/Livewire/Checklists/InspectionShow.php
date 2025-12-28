<?php

namespace App\Livewire\Checklists;

use App\Models\DynamicInspection;
use Livewire\Component;

class InspectionShow extends Component
{
    public DynamicInspection $inspection;

    public function mount(int $id)
    {
        $this->inspection = DynamicInspection::with([
            'checklist',
            'user',
            'answers.question'
        ])->findOrFail($id);
    }

    public function render()
    {
        // Organizar respuestas por orden de pregunta
        $answers = $this->inspection->answers->sortBy(function ($answer) {
            return $answer->question->order ?? 0;
        });

        return view('livewire.checklists.inspection-show', [
            'answers' => $answers,
        ])->layout('components.layouts.app');
    }
}
