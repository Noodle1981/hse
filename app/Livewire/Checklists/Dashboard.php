<?php

namespace App\Livewire\Checklists;

use App\Models\DynamicChecklist;
use App\Models\DynamicInspection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        // KPIs principales
        $stats = [
            'total_checklists' => DynamicChecklist::count(),
            'total_inspections' => DynamicInspection::count(),
            'completed_this_month' => DynamicInspection::whereMonth('completed_at', now()->month)->count(),
            'active_checklists' => DynamicChecklist::where('is_active', true)->count(),
        ];

        // Inspecciones por checklist
        $inspectionsByChecklist = DynamicInspection::select('dynamic_checklist_id', DB::raw('count(*) as total'))
            ->groupBy('dynamic_checklist_id')
            ->with('checklist:id,title')
            ->get()
            ->map(fn($item) => [
                'checklist' => $item->checklist->title,
                'total' => $item->total
            ]);

        // Inspecciones por día (últimos 30 días)
        $inspectionsPerDay = DynamicInspection::select(
                DB::raw('DATE(completed_at) as date'),
                DB::raw('count(*) as total')
            )
            ->where('completed_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($item) => [
                'date' => $item->date,
                'total' => $item->total
            ]);

        // Inspecciones recientes
        $recentInspections = DynamicInspection::with(['checklist:id,title', 'user:id,nombres,apellidos'])
            ->latest('completed_at')
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
