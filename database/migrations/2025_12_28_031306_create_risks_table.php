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
        Schema::create('risks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('risk_matrix_id')->constrained()->onDelete('cascade');
            $table->string('hazard'); // "Corte con sierra", "Caída a distinto nivel"
            $table->string('type')->nullable(); // "Mecánico", "Químico", "Ergonómico", etc.
            $table->text('consequence')->nullable(); // "Amputación", "Fractura", etc.
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risks');
    }
};
