<?php

namespace App\Livewire\Checklists;

use App\Models\DynamicChecklist;
use App\Models\DynamicInspection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isTech = $user->rol === 'Técnico';

        // KPIs principales
        $stats = [
            'total_checklists' => DynamicChecklist::where('is_active', true)->count(),
            'active_checklists' => DynamicChecklist::where('is_active', true)->count(),
            'total_inspections' => DynamicInspection::query()
                ->when($isTech, fn($q) => $q->where('user_id', $user->id))
                ->count(),
            'completed_this_month' => DynamicInspection::query()
                ->when($isTech, fn($q) => $q->where('user_id', $user->id))
                ->whereMonth('completed_at', now()->month)
                ->count(),
            'pending_tasks' => \App\Models\ChecklistAssignment::query()
                ->when($isTech, fn($q) => $q->where('user_id', $user->id))
                ->where('status', 'pending')
                ->count(),
        ];

        // Inspecciones por checklist (Solo del técnico si corresponde)
        $inspectionsByChecklist = DynamicInspection::select('dynamic_checklist_id', DB::raw('count(*) as total'))
            ->when($isTech, fn($q) => $q->where('user_id', $user->id))
            ->groupBy('dynamic_checklist_id')
            ->with('checklist:id,title')
            ->get()
            ->map(fn($item) => [
                'checklist' => $item->checklist->title ?? 'N/A',
                'total' => $item->total
            ]);

        // Inspecciones por día (Solo del técnico si corresponde)
        $inspectionsPerDay = DynamicInspection::select(
                DB::raw('DATE(completed_at) as date'),
                DB::raw('count(*) as total')
            )
            ->when($isTech, fn($q) => $q->where('user_id', $user->id))
            ->where('completed_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($item) => [
                'date' => $item->date,
                'total' => $item->total
            ]);

        // Inspecciones recientes (Solo del técnico si corresponde)
        $recentInspections = DynamicInspection::with(['checklist:id,title', 'user:id,nombres,apellidos'])
            ->when($isTech, fn($q) => $q->where('user_id', $user->id))
            ->latest()
            ->limit(10)
            ->get();

        return view('livewire.checklists.dashboard', compact(
            'stats',
            'inspectionsByChecklist',
            'inspectionsPerDay',
            'recentInspections'
        ));
    }
}
