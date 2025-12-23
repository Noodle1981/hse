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
        Schema::create('checklist_aus', function (Blueprint $table) {
            $table->id();
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('n_serie')->nullable();
            $table->integer('capacidad')->nullable();
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
            $table->string('n16', length: 9)->nullable();
            $table->string('n17', length: 9)->nullable();
            $table->string('n18', length: 9)->nullable();
            $table->string('n19', length: 9)->nullable();
            $table->string('n20', length: 9)->nullable();
            $table->string('n21', length: 9)->nullable();
            $table->string('n22', length: 9)->nullable();
            $table->string('n23', length: 9)->nullable();
            $table->string('n24', length: 9)->nullable();
            $table->string('n25', length: 9)->nullable();
            $table->string('n26', length: 9)->nullable();
            $table->string('n27', length: 9)->nullable();
            $table->string('n28', length: 9)->nullable();
            $table->string('n29', length: 9)->nullable();
            $table->string('n30', length: 9)->nullable();
            $table->string('n31', length: 9)->nullable();
            $table->string('n32', length: 9)->nullable();
            $table->string('n33', length: 9)->nullable();
            $table->foreignId('checklist_asign_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_aus');
    }
};
