<?php

namespace App\Livewire\Checklists;

use App\Models\DynamicInspection;
use Livewire\Component;

class InspectionShow extends Component
{
    public DynamicInspection $inspection;

    public function mount(int $id)
    {
        $inspection = DynamicInspection::with([
            'checklist',
            'user',
            'sede.empresa',
            'answers.question'
        ])->findOrFail($id);

        // Seguridad: El técnico solo puede ver sus propias inspecciones
        if (auth()->user()->rol === 'Técnico' && $inspection->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver esta inspección');
        }

        $this->inspection = $inspection;
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
