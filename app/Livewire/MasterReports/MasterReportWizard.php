<?php

namespace App\Livewire\MasterReports;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MasterReportWizard extends Component
{
    public $mode = 'create'; // create, edit
    public $reportId;
    public $report;

    public $sede_id;
    public $user_id; // Técnico
    public $titulo;
    public $periodo; // YYYY-MM
    public $resumen_ejecutivo;
    public $observaciones_matriz;
    public $observaciones_visitas;
    public $observaciones_checklists;
    public $observaciones_capacitaciones;
    public $observaciones_hallazgos_incidentes;
    public $signature; // Base64 Drawn Signature
    
    public $currentStep = 1;
    public $monthlyData = [];

    protected $rules = [
        'sede_id' => 'required|exists:sedes,id',
        'user_id' => 'required|exists:users,id',
        'titulo' => 'required|string|max:255',
        'periodo' => 'required|string|max:10', // Puede ser YYYY-MM o YYYY-QX
    ];

    public function mount($mode = 'create', $reportId = null)
    {
        $this->mode = $mode;
        $this->reportId = $reportId;

        if ($this->mode === 'edit' && $this->reportId) {
            $this->report = \App\Models\MasterReport::findOrFail($this->reportId);
            $this->sede_id = $this->report->sede_id;
            $this->user_id = $this->report->user_id;
            $this->titulo = $this->report->titulo;
            $this->periodo = $this->report->periodo;
            $this->resumen_ejecutivo = $this->report->resumen_ejecutivo;
            $this->observaciones_matriz = $this->report->observaciones_matriz;
            $this->observaciones_visitas = $this->report->observaciones_visitas;
            $this->observaciones_checklists = $this->report->observaciones_checklists;
            $this->observaciones_capacitaciones = $this->report->observaciones_capacitaciones;
            $this->observaciones_hallazgos_incidentes = $this->report->observaciones_hallazgos_incidentes;
            $this->signature = $this->report->datos_anexo['firma_tecnico'] ?? null;
            $this->loadMonthlyData();
        } else {
            $this->periodo = now()->format('Y-m');
            $this->titulo = 'Informe Maestro de Servicio - ' . now()->translatedFormat('F Y');
        }
    }

    public function loadMonthlyData()
    {
        if (!$this->sede_id || !$this->periodo) return;

        $period = $this->periodo;
        if (str_contains($period, '-Q')) {
            $q = (int)substr($period, -1);
            $y = (int)substr($period, 0, 4);
            $startMonth = ($q - 1) * 3 + 1;
            $startDate = \Carbon\Carbon::create($y, $startMonth, 1)->startOfMonth();
            $endDate = \Carbon\Carbon::create($y, $startMonth + 2, 1)->endOfMonth();
        } else {
            try {
                $startDate = \Carbon\Carbon::parse($period . '-01')->startOfMonth();
                $endDate = \Carbon\Carbon::parse($period . '-01')->endOfMonth();
            } catch (\Exception $e) {
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
            }
        }

        $sede = \App\Models\Sede::find($this->sede_id);
        if (!$sede) return;

        $visits = $sede->visitCertificates()
            ->with([
                'checklistCompletions.assignment.checklist', 
                'trainings', 
                'hallazgos.fotos', 
                'incidentes.fotos'
            ])
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->whereIn('status', ['completed', 'signed'])
            ->get();

        $this->monthlyData = [
            'visits' => $visits,
            'checklists' => $visits->flatMap->checklistCompletions,
            'trainings' => $visits->flatMap->trainings,
            'hallazgos' => $visits->flatMap->hallazgos,
            'incidentes' => $visits->flatMap->incidentes,
            'annual_plan_status' => $this->calculateAnnualPlanStatus($sede, $startDate->year),
        ];
    }


    private function calculateAnnualPlanStatus($sede, $year)
    {
        // Lógica simplificada de cumplimiento
        $plans = $sede->annualPlans()->where('year', $year)->get();
        if ($plans->isEmpty()) return 0;

        $totalActivities = $plans->count() * 12;
        $completed = 0; // Aquí iría la lógica real comparativa
        
        return 75; // Mock por ahora
    }

    public function save()
    {
        $this->validate();

        if ($this->mode === 'create') {
            \App\Models\MasterReport::create([
                'sede_id' => $this->sede_id,
                'user_id' => $this->user_id,
                'solicitado_por' => Auth::id(),
                'titulo' => $this->titulo,
                'periodo' => $this->periodo,
                'estado' => 'solicitado',
            ]);
            session()->flash('success', 'Informe solicitado exitosamente.');
            return redirect()->route('gestor.master-reports.index');
        } else {
            $datos_anexo = $this->report->datos_anexo ?? [];
            if ($this->signature) {
                $datos_anexo['firma_tecnico'] = $this->signature;
            }

            $this->report->update([
                'resumen_ejecutivo' => $this->resumen_ejecutivo,
                'observaciones_matriz' => $this->observaciones_matriz,
                'observaciones_visitas' => $this->observaciones_visitas,
                'observaciones_checklists' => $this->observaciones_checklists,
                'observaciones_capacitaciones' => $this->observaciones_capacitaciones,
                'observaciones_hallazgos_incidentes' => $this->observaciones_hallazgos_incidentes,
                'datos_anexo' => $datos_anexo,
                'estado' => 'finalizado',
            ]);
            session()->flash('success', 'Informe finalizado exitosamente.');
            return redirect()->route('tecnico.master-reports.index');
        }
    }

    public function render()
    {
        return view('livewire.master-reports.master-report-wizard', [
            'sedes' => \App\Models\Sede::all(),
            'tecnicos' => \App\Models\User::where('rol', \App\Models\User::ROLE_TECNICO)->get(),
        ]);
    }
}
