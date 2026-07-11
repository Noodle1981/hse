<?php

namespace App\Console\Commands;

use App\Models\ChecklistSection;
use App\Models\DynamicQuestion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixLooseQuestionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ep:fix-loose-questions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Agrupa preguntas huérfanas sin sección en una sección por defecto llamada "Inspección General"';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando corrección de preguntas sueltas...');

        // Buscar preguntas donde section_id es nulo
        $looseQuestions = DynamicQuestion::whereNull('section_id')->get();

        if ($looseQuestions->isEmpty()) {
            $this->info('No se encontraron preguntas sueltas sin sección en la base de datos.');

            return self::SUCCESS;
        }

        $this->info("Se encontraron {$looseQuestions->count()} preguntas sueltas.");

        // Agrupar preguntas por checklist
        $groupedByChecklist = $looseQuestions->groupBy('dynamic_checklist_id');

        DB::transaction(function () use ($groupedByChecklist) {
            foreach ($groupedByChecklist as $checklistId => $questions) {
                // Buscar si ya existe la sección "Inspección General" para este checklist
                $section = ChecklistSection::where('dynamic_checklist_id', $checklistId)
                    ->where('title', 'Inspección General')
                    ->first();

                if (! $section) {
                    $this->line("Creando sección 'Inspección General' para el Checklist #{$checklistId}...");

                    // Si ya hay secciones, opcionalmente desplazar su orden
                    ChecklistSection::where('dynamic_checklist_id', $checklistId)
                        ->increment('order');

                    $section = ChecklistSection::create([
                        'dynamic_checklist_id' => $checklistId,
                        'title' => 'Inspección General',
                        'description' => 'Preguntas generales del checklist.',
                        'order' => 0,
                    ]);
                }

                $questionIds = $questions->pluck('id')->toArray();
                $this->line('Asignando '.count($questionIds)." preguntas al ID de Sección #{$section->id}...");

                DynamicQuestion::whereIn('id', $questionIds)
                    ->update(['section_id' => $section->id]);
            }
        });

        $this->info('¡Proceso de corrección finalizado con éxito!');

        return self::SUCCESS;
    }
}
