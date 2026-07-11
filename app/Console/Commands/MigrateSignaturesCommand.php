<?php

namespace App\Console\Commands;

use App\Models\DynamicInspection;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MigrateSignaturesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inspections:migrate-signatures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra firmas base64 a archivos en storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando migración de firmas de inspecciones...');

        DynamicInspection::whereNotNull('technician_signature')
            ->where('technician_signature', 'like', 'data:image%')
            ->chunk(100, function ($inspections) {
                foreach ($inspections as $inspection) {
                    $data = $inspection->technician_signature;
                    $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $data);
                    $fileName = 'signatures/'.Str::random(40).'.png';
                    Storage::disk('public')->put($fileName, base64_decode($base64));

                    $inspection->technician_signature = $fileName;
                    $inspection->save();

                    $this->line("Migrada firma técnica para inspección ID: {$inspection->id}");
                }
            });

        DynamicInspection::whereNotNull('supervisor_signature')
            ->where('supervisor_signature', 'like', 'data:image%')
            ->chunk(100, function ($inspections) {
                foreach ($inspections as $inspection) {
                    $data = $inspection->supervisor_signature;
                    $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $data);
                    $fileName = 'signatures/'.Str::random(40).'.png';
                    Storage::disk('public')->put($fileName, base64_decode($base64));

                    $inspection->supervisor_signature = $fileName;
                    $inspection->save();

                    $this->line("Migrada firma de supervisor para inspección ID: {$inspection->id}");
                }
            });

        $this->info('Migración completada con éxito.');
    }
}
