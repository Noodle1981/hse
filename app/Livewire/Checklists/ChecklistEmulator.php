<?php

namespace App\Livewire\Checklists;

use Livewire\Component;
use App\Models\DynamicChecklist;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ChecklistEmulator extends Component
{
    public DynamicChecklist $checklist;

    public function mount(DynamicChecklist $checklist)
    {
        $this->checklist = $checklist->load(['sections', 'questions']);
    }

    public function render()
    {
        // Agrupar preguntas por sección
        $groupedQuestions = [];
        $unsectionedQuestions = [];

        foreach ($this->checklist->questions as $question) {
            if ($question->section_id) {
                $groupedQuestions[$question->section_id][] = $question;
            } else {
                $unsectionedQuestions[] = $question;
            }
        }

        return view('livewire.checklists.checklist-emulator', [
            'groupedQuestions' => $groupedQuestions,
            'unsectionedQuestions' => $unsectionedQuestions,
        ]);
    }
}
