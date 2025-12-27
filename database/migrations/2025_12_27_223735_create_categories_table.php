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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // 'hys', 'calidad', etc.
            $table->string('name'); // 'Higiene y Seguridad Laboral'
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#6B7280'); // Color hex para UI
            $table->string('icon', 50)->nullable(); // Icono (ej: 'shield-check')
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0); // Orden de visualización
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
