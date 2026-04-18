<?php

namespace App\Livewire\Checklists;

use App\Models\DynamicInspection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class InspectionShow extends \Livewire\Component
{
    public DynamicInspection $inspection;

    #[\Livewire\Attributes\Url]
    public ?int $visit_id = null;

    public function mount(int $id)
    {
        $inspection = DynamicInspection::with([
            'checklist',
            'user',
            'sede.empresa',
            'answers.question'
        ])->findOrFail($id);

        // Seguridad: El técnico solo puede ver sus propias inspecciones
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user && $user->rol === 'Técnico' && $inspection->user_id !== Auth::id()) {
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
        ]);
    }
}
