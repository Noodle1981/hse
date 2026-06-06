<?php

namespace Database\Seeders;

use App\Models\ChecklistSection;
use App\Models\DynamicChecklist;
use App\Models\DynamicQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ChecklistsFromJsonSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/seeds/checklists_export.json');

        if (! File::exists($path)) {
            $this->command->error("❌ No se encontró el archivo: {$path}");
            $this->command->info('   Primero ejecuta: php artisan checklists:export');

            return;
        }

        $checklists = json_decode(File::get($path), true);
        $imported = 0;

        foreach ($checklists as $data) {
            // Evitar duplicados por código de registro o título
            $existing = DynamicChecklist::where('registry_code', $data['registry_code'])
                ->orWhere('title', $data['title'])
                ->first();

            if ($existing) {
                $this->command->warn("⚠️  Ya existe checklist: \"{$data['title']}\" — omitido");

                continue;
            }

            $checklist = DynamicChecklist::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'registry_code' => $data['registry_code'],
                'is_active' => $data['is_active'] ?? true,
                'category_id' => null, // Temporal para version-minina
                'subcategory_id' => null, // Temporal para version-minina
            ]);

            // Crear preguntas sin sección
            foreach ($data['questions'] ?? [] as $qData) {
                DynamicQuestion::create([
                    'dynamic_checklist_id' => $checklist->id,
                    'section_id' => null,
                ] + $this->questionFields($qData));
            }

            // Crear secciones y sus preguntas
            foreach ($data['sections'] ?? [] as $sData) {
                $section = ChecklistSection::create([
                    'dynamic_checklist_id' => $checklist->id,
                    'title' => $sData['title'],
                    'description' => $sData['description'] ?? null,
                    'order' => $sData['order'],
                ]);

                foreach ($sData['questions'] ?? [] as $qData) {
                    DynamicQuestion::create([
                        'dynamic_checklist_id' => $checklist->id,
                        'section_id' => $section->id,
                    ] + $this->questionFields($qData));
                }
            }

            $this->command->info("✅ Importado: \"{$checklist->title}\"");
            $imported++;
        }

        $this->command->info("\n🎉 Total importados: {$imported} checklists");
    }

    private function questionFields(array $q): array
    {
        return [
            'label' => $q['label'],
            'type' => $q['type'],
            'options' => $q['options'] ? json_encode($q['options']) : null,
            'failure_values' => $q['failure_values'] ? json_encode($q['failure_values']) : null,
            'min_value' => $q['min_value'],
            'max_value' => $q['max_value'],
            'order' => $q['order'],
            'is_required' => $q['is_required'],
            'help_text' => $q['help_text'],
        ];
    }
}
