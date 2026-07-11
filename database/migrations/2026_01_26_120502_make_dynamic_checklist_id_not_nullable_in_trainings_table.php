<?php

use App\Models\DynamicChecklist;
use App\Models\Training;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Asignar un checklist por defecto a los registros existentes que tengan NULL
        $defaultChecklist = DynamicChecklist::active()->first();

        if ($defaultChecklist) {
            Training::whereNull('dynamic_checklist_id')
                ->update(['dynamic_checklist_id' => $defaultChecklist->id]);
        }

        // 2. Hacer la columna NOT NULL
        Schema::table('trainings', function (Blueprint $table) {
            $table->foreignId('dynamic_checklist_id')
                ->nullable(false)
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->foreignId('dynamic_checklist_id')
                ->nullable()
                ->change();
        });
    }
};
