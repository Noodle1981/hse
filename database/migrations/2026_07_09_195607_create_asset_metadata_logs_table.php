<?php

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
        Schema::create('asset_metadata_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('asset_id')->constrained('assets')->cascadeOnDelete();
            // Usamos dynamic_inspection_id para coincidir con la nomenclatura del sistema (modelo DynamicInspection)
            $table->foreignId('dynamic_inspection_id')->constrained('dynamic_inspections')->cascadeOnDelete();
            $table->string('field_key');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['asset_id', 'field_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_metadata_logs');
    }
};
