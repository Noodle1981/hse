<?php

namespace App\Console\Commands;

use App\Models\DynamicChecklist;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportChecklists extends Command
{
    protected $signature = 'checklists:export {--path= : Output path (default: database/seeds/checklists_export.json)}';
    protected $description = 'Exporta todos los checklists activos a un archivo JSON para poder restaurarlos (seeder)';

    public function handle(): int
    {
        $checklists = DynamicChecklist::with(['sections' => function ($q) {
            $q->orderBy('order');
        }, 'sections.questions' => function ($q) {
            $q->orderBy('order');
        }, 'questions' => function ($q) {
            $q->whereNull('section_id')->orderBy('order');
        }])->get();

        $data = $checklists->map(function (DynamicChecklist $cl) {
            return [
                'title'           => $cl->title,
                'description'     => $cl->description,
                'registry_code'   => $cl->registry_code,
                'is_active'       => $cl->is_active,
                'category_id'     => $cl->category_id,
                'subcategory_id'  => $cl->subcategory_id,
                'sections' => $cl->sections->map(function ($section) {
                    return [
                        'title'       => $section->title,
                        'description' => $section->description ?? null,
                        'order'       => $section->order,
                        'questions'   => $section->questions->map(fn ($q) => $this->mapQuestion($q))->values()->toArray(),
                    ];
                })->values()->toArray(),
                'questions' => $cl->questions->map(fn ($q) => $this->mapQuestion($q))->values()->toArray(),
            ];
        })->toArray();

        $path = $this->option('path') ?? base_path('database/seeds/checklists_export.json');
        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("✅ " . count($data) . " checklists exportados a: {$path}");
        return 0;
    }

    private function mapQuestion($q): array
    {
        return [
            'label'          => $q->label,
            'type'           => $q->type,
            'options'        => $q->options,
            'failure_values' => $q->failure_values,
            'min_value'      => $q->min_value,
            'max_value'      => $q->max_value,
            'order'          => $q->order,
            'is_required'    => $q->is_required,
            'help_text'      => $q->help_text,
        ];
    }
}
