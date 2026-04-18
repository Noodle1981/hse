<?php

namespace App\Livewire\Checklists;

use Livewire\Component;
use App\Models\DynamicChecklist;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ChecklistEmulator extends Component
{
    public DynamicChecklist $checklist;
    public array $answers = [];

    public function mount(DynamicChecklist $checklist)
    {
        $this->checklist = $checklist->load(['sections', 'questions']);
    }

    public function render()
    {
        $questions = $this->checklist->questions;
        
        $groupedQuestions = $questions->whereNotNull('section_id')->groupBy('section_id');
        $unsectionedQuestions = $questions->whereNull('section_id');

        return view('livewire.checklists.checklist-emulator', [
            'groupedQuestions' => $groupedQuestions,
            'unsectionedQuestions' => $unsectionedQuestions,
        ]);
    }
}
