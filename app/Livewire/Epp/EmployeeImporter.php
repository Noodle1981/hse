<?php

namespace App\Livewire\Epp;

use App\Models\Employee;
use App\Models\Empresa;
use App\Models\Sede;
use Livewire\Component;
use Livewire\WithFileUploads;
use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLSXGen;

class EmployeeImporter extends Component
{
    use WithFileUploads;

    public $file;
    public $empresa_id;
    public $sede_id;
    public $importing = false;
    public $message = '';
    public $errorLog = [];

    protected $rules = [
        'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        'empresa_id' => 'required|exists:empresas,id',
        'sede_id' => 'required|exists:sedes,id',
    ];

    public function downloadTemplate()
    {
        $header = [
            ['nombre', 'apellido', 'dni', 'cuil', 'puesto']
        ];
        
        $data = [
            ['Juan', 'Perez', '12345678', '20-12345678-9', 'Operario'],
            ['Maria', 'Garcia', '87654321', '27-87654321-0', 'Supervisor']
        ];

        $xlsx = SimpleXLSXGen::fromArray(array_merge($header, $data));
        
        return response()->streamDownload(function () use ($xlsx) {
            echo $xlsx;
        }, 'plantilla_empleados.xlsx');
    }

    public function import()
    {
        $this->validate();
        $this->importing = true;
        $this->errorLog = [];
        $this->message = '';

        $path = $this->file->getRealPath();
        
        if ($xlsx = SimpleXLSX::parse($path)) {
            $rows = $xlsx->rows();
            $header = array_shift($rows); // Quitar encabezados
            
            // Mapeo de columnas por nombre
            $colMap = array_flip(array_map('strtolower', $header));
            
            $count = 0;
            $errors = 0;

            foreach ($rows as $index => $row) {
                try {
                    $nombre = $row[$colMap['nombre']] ?? null;
                    $apellido = $row[$colMap['apellido']] ?? null;
                    $dni = $row[$colMap['dni']] ?? null;
                    $cuil = $row[$colMap['cuil']] ?? null;
                    $puesto = $row[$colMap['puesto']] ?? null;

                    if (!$nombre || !$apellido || !$dni) {
                        $this->errorLog[] = "Fila " . ($index + 2) . ": Datos obligatorios faltantes (Nombre, Apellido o DNI).";
                        $errors++;
                        continue;
                    }

                    Employee::updateOrCreate(
                        ['dni' => $dni],
                        [
                            'empresa_id' => $this->empresa_id,
                            'sede_id' => $this->sede_id,
                            'name' => $nombre,
                            'last_name' => $apellido,
                            'cuil' => $cuil,
                            'puesto' => $puesto,
                            'is_active' => true,
                        ]
                    );
                    $count++;
                } catch (\Exception $e) {
                    $this->errorLog[] = "Fila " . ($index + 2) . ": " . $e->getMessage();
                    $errors++;
                }
            }

            $this->message = "Importación finalizada. Creados/Actualizados: $count. Errores: $errors.";
            if ($errors == 0) {
                $this->reset(['file', 'empresa_id', 'sede_id']);
            }
        } else {
            $this->message = "Error al procesar el archivo: " . SimpleXLSX::parseError();
        }

        $this->importing = false;
    }

    public function render()
    {
        $empresas = Empresa::orderBy('nombre')->get();
        $sedes = $this->empresa_id 
            ? Sede::where('empresa_id', $this->empresa_id)->orderBy('nombre_sede')->get() 
            : collect();

        return view('livewire.epp.employee-importer', [
            'empresas' => $empresas,
            'sedes' => $sedes,
        ])->layout('components.layouts.app');
    }
}
