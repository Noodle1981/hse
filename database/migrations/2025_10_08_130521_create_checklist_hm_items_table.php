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
        Schema::create('checklist_hm_items', function (Blueprint $table) {
            $table->id();
            $table->string('herramienta')->nullable();
            $table->string('condiciones')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('checklist_hm_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_hm_items');
    }
};
