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
        Schema::create('checklist_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_checklist_id')->constrained()->cascadeOnDelete();
            $table->string('title'); // "Ambiente de trabajo", "ACTITUD HACIA LA SEGURIDAD"
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index(['dynamic_checklist_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_sections');
    }
};
