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
        Schema::create('checklist_e_s', function (Blueprint $table) {
            $table->id();
            $table->string('lugar')->nullable();
            $table->string('n1', length: 9)->nullable();
            $table->string('n2', length: 9)->nullable();
            $table->string('n3', length: 9)->nullable();
            $table->string('n4', length: 9)->nullable();
            $table->string('n5', length: 9)->nullable();
            $table->string('n6', length: 9)->nullable();
            $table->string('n7', length: 9)->nullable();
            $table->string('n8', length: 9)->nullable();
            $table->string('n9', length: 9)->nullable();
            $table->string('n10', length: 9)->nullable();
            $table->string('n11', length: 9)->nullable();
            $table->string('n12', length: 9)->nullable();
            $table->string('n13', length: 9)->nullable();
            $table->string('n14', length: 9)->nullable();
            $table->string('n15', length: 9)->nullable();
            $table->foreignId('checklist_asign_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_e_s');
    }
};
