<?php

namespace App\Livewire\Checklists;

use App\Models\DynamicInspection;
use Livewire\Component;
use Livewire\WithPagination;

class InspectionHistory extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public ?int $checklistFilter = null;

    protected $queryString = ['search', 'statusFilter', 'checklistFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $inspections = DynamicInspection::query()
            ->with(['checklist', 'user'])
            ->when($this->search, function ($query) {
                $query->whereHas('checklist', function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%');
                })->orWhereHas('user', function ($q) {
                    $q->where('nombres', 'like', '%' . $this->search . '%')
                      ->orWhere('apellidos', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->checklistFilter, function ($query) {
                $query->where('dynamic_checklist_id', $this->checklistFilter);
            })
            ->latest('completed_at')
            ->paginate(15);

        return view('livewire.checklists.inspection-history', [
            'inspections' => $inspections,
        ])->layout('layouts.app');
    }
}
