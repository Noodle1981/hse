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
        Schema::create('hallazgo_seguimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hallazgo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->comment('Usuario que realiza el seguimiento');
            $table->string('estado_anterior');
            $table->string('estado_nuevo');
            $table->text('comentario')->nullable();
            $table->dateTime('fecha_seguimiento');
            $table->timestamps();

            $table->index('hallazgo_id');
            $table->index('fecha_seguimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hallazgo_seguimientos');
    }
};
