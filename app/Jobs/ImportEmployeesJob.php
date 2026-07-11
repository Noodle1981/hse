<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Rules\ValidCuil;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Shuchkin\SimpleXLSX;

class ImportEmployeesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    protected $empresaId;

    protected $sedeId;

    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath, int $empresaId, int $sedeId, int $userId)
    {
        $this->filePath = $filePath;
        $this->empresaId = $empresaId;
        $this->sedeId = $sedeId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $errorLog = [];
        $count = 0;
        $errors = 0;

        try {
            if ($xlsx = SimpleXLSX::parse($this->filePath)) {
                $rows = $xlsx->rows();
                $header = array_shift($rows);

                $colMap = array_flip(array_map('strtolower', $header));

                // Procesar en chunks de 50
                foreach (array_chunk($rows, 50) as $chunk) {
                    DB::transaction(function () use ($chunk, $colMap, &$count, &$errors, &$errorLog) {
                        foreach ($chunk as $index => $row) {
                            try {
                                $nombre = $row[$colMap['nombre']] ?? null;
                                $apellido = $row[$colMap['apellido']] ?? null;
                                $dni = $row[$colMap['dni']] ?? null;
                                $cuil = $row[$colMap['cuil']] ?? null;
                                $puesto = $row[$colMap['puesto']] ?? null;

                                if (! $nombre || ! $apellido || ! $dni) {
                                    $errorLog[] = 'Fila '.($index + 2).': Datos obligatorios faltantes.';
                                    $errors++;

                                    continue;
                                }

                                // Validar CUIL si está presente
                                if ($cuil) {
                                    $validator = Validator::make(['cuil' => $cuil], [
                                        'cuil' => [new ValidCuil],
                                    ]);
                                    if ($validator->fails()) {
                                        $errorLog[] = 'Fila '.($index + 2).": CUIL inválido ($cuil).";
                                        $errors++;

                                        continue;
                                    }
                                }

                                Employee::updateOrCreate(
                                    ['dni' => $dni],
                                    [
                                        'empresa_id' => $this->empresaId,
                                        'sede_id' => $this->sedeId,
                                        'name' => $nombre,
                                        'last_name' => $apellido,
                                        'cuil' => $cuil,
                                        'puesto' => $puesto,
                                        'is_active' => true,
                                    ]
                                );
                                $count++;
                            } catch (\Exception $e) {
                                $errorLog[] = 'Fila '.($index + 2).': '.$e->getMessage();
                                $errors++;
                            }
                        }
                    });
                }

                Log::info('Importación de empleados completada', [
                    'user_id' => $this->userId,
                    'count' => $count,
                    'errors' => $errors,
                ]);

                // TODO: Notificar al usuario (email o notificación)

            } else {
                Log::error('Error al parsear archivo Excel', [
                    'file' => $this->filePath,
                    'error' => SimpleXLSX::parseError(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error en ImportEmployeesJob', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        } finally {
            // Limpiar archivo temporal
            if (file_exists($this->filePath)) {
                @unlink($this->filePath);
            }
        }
    }
}
