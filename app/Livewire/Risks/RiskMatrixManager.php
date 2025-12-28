<?php

namespace App\Livewire\Risks;

use App\Models\RiskMatrix;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\Risk;
use App\Models\RiskControl;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class RiskMatrixManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showForm = false;
    public $matrixId = null;

    // Campos del formulario
    public $title, $description, $area, $process, $empresa_id, $sede_id, $status = 'draft';
    public $sedes = [];
    
    // Para validación de duplicados
    public $originalTitle = '';

    protected function rules()
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if ($this->originalTitle && $value === $this->originalTitle) {
                        $fail('El título debe ser diferente al original para poder guardar la copia.');
                    }
                },
            ],
            'description' => 'nullable|string',
            'area' => 'nullable|string|max:100',
            'process' => 'nullable|string|max:100',
            'empresa_id' => 'required|exists:empresas,id',
            'sede_id' => 'required|exists:sedes,id',
            'status' => 'required|in:draft,active,archived',
        ];
    }

    public function updatedEmpresaId($value)
    {
        $this->sedes = Sede::where('empresa_id', $value)->get();
        $this->sede_id = null;
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $matrix = RiskMatrix::findOrFail($id);
        $this->matrixId = $matrix->id;
        $this->title = $matrix->title;
        $this->description = $matrix->description;
        $this->area = $matrix->area;
        $this->process = $matrix->process;
        $this->empresa_id = $matrix->empresa_id;
        $this->updatedEmpresaId($this->empresa_id);
        $this->sede_id = $matrix->sede_id;
        $this->status = $matrix->status;
        $this->originalTitle = ''; // En edición normal no bloqueamos el mismo título
        $this->showForm = true;
    }

    public function duplicate($id)
    {
        $source = RiskMatrix::findOrFail($id);
        $this->resetForm();
        
        $this->title = $source->title . ' (COPIA)';
        $this->originalTitle = $source->title; // Guardamos el original para comparar
        
        $this->description = $source->description;
        $this->area = $source->area;
        $this->process = $source->process;
        $this->empresa_id = $source->empresa_id;
        $this->updatedEmpresaId($this->empresa_id);
        $this->sede_id = $source->sede_id;
        $this->status = 'draft'; // Las copias empiezan como borrador
        
        // El ID de la matriz fuente lo guardamos temporalmente si queremos copiar sus riesgos
        $this->sourceMatrixId = $id;
        
        $this->showForm = true;
    }

    public $sourceMatrixId = null;

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            if ($this->matrixId) {
                // Actualizar existente
                RiskMatrix::find($this->matrixId)->update([
                    'title' => $this->title,
                    'description' => $this->description,
                    'area' => $this->area,
                    'process' => $this->process,
                    'empresa_id' => $this->empresa_id,
                    'sede_id' => $this->sede_id,
                    'status' => $this->status,
                ]);
                session()->flash('message', 'Matriz actualizada correctamente.');
            } else {
                // Crear nueva (puede ser copia o nueva de cero)
                $newMatrix = RiskMatrix::create([
                    'title' => $this->title,
                    'description' => $this->description,
                    'area' => $this->area,
                    'process' => $this->process,
                    'empresa_id' => $this->empresa_id,
                    'sede_id' => $this->sede_id,
                    'user_id' => auth()->id(),
                    'status' => $this->status,
                ]);

                // Si viene de un duplicado, copiamos riesgos y controles
                if ($this->sourceMatrixId) {
                    $source = RiskMatrix::with('risks.controls')->find($this->sourceMatrixId);
                    foreach ($source->risks as $oldRisk) {
                        $newRisk = $newMatrix->risks()->create([
                            'hazard' => $oldRisk->hazard,
                            'type' => $oldRisk->type,
                            'consequence' => $oldRisk->consequence,
                            'order' => $oldRisk->order,
                        ]);

                        foreach ($oldRisk->controls as $oldControl) {
                            $newRisk->controls()->create([
                                'measure' => $oldControl->measure,
                                'type' => $oldControl->type,
                                'status' => 'pendiente', // Los controles de la copia vuelven a pendiente
                                'due_date' => $oldControl->due_date,
                                'checklist_id' => $oldControl->checklist_id,
                            ]);
                        }
                    }
                }
                
                session()->flash('message', $this->sourceMatrixId ? 'Matriz duplicada correctamente.' : 'Matriz creada correctamente.');
            }
        });

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        RiskMatrix::find($id)->delete();
        session()->flash('message', 'Matriz eliminada.');
    }

    private function resetForm()
    {
        $this->matrixId = null;
        $this->sourceMatrixId = null;
        $this->title = '';
        $this->originalTitle = '';
        $this->description = '';
        $this->area = '';
        $this->process = '';
        $this->empresa_id = null;
        $this->sede_id = null;
        $this->status = 'draft';
        $this->sedes = [];
    }

    public function render()
    {
        $user = auth()->user();
        
        $matrices = RiskMatrix::with(['empresa', 'sede'])
            ->when($user->rol === 'Técnico', function($q) use ($user) {
                // El técnico solo ve matrices activas de las sedes a las que está asignado
                $sedeIds = $user->sedes()->pluck('sedes.id');
                $q->whereIn('sede_id', $sedeIds)->where('status', 'active');
            })
            ->when($this->search, function($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('area', 'like', "%{$this->search}%")
                  ->orWhere('process', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        return view('livewire.risks.risk-matrix-manager', [
            'matrices' => $matrices,
            'empresas' => $user->rol === 'Administrador' ? Empresa::all() : [],
        ])->layout('components.layouts.app');
    }
}
