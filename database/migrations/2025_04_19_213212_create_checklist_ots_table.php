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
        Schema::create('checklist_ots', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion')->nullable();
            $table->tinyInteger('n1')->nullable();
            $table->tinyInteger('n2')->nullable();
            $table->tinyInteger('n3')->nullable();
            $table->tinyInteger('n4')->nullable();
            $table->tinyInteger('n5')->nullable();
            $table->tinyInteger('n6')->nullable();
            $table->tinyInteger('n7')->nullable();
            $table->tinyInteger('n8')->nullable();
            $table->tinyInteger('n9')->nullable();
            $table->tinyInteger('n10')->nullable();
            $table->tinyInteger('n11')->nullable();
            $table->tinyInteger('n12')->nullable();
            $table->tinyInteger('n13')->nullable();
            $table->tinyInteger('n14')->nullable();
            $table->tinyInteger('n15')->nullable();
            $table->tinyInteger('n16')->nullable();
            $table->tinyInteger('n17')->nullable();
            $table->tinyInteger('n18')->nullable();
            $table->tinyInteger('n19')->nullable();
            $table->tinyInteger('n20')->nullable();
            $table->tinyInteger('n21')->nullable();
            $table->tinyInteger('n22')->nullable();
            $table->tinyInteger('n23')->nullable();
            $table->tinyInteger('n24')->nullable();
            $table->tinyInteger('n25')->nullable();
            $table->tinyInteger('n26')->nullable();
            $table->tinyInteger('n27')->nullable();
            $table->tinyInteger('n28')->nullable();
            $table->tinyInteger('n29')->nullable();
            $table->tinyInteger('n30')->nullable();
            $table->tinyInteger('n31')->nullable();
            $table->tinyInteger('n32')->nullable();
            $table->tinyInteger('n33')->nullable();
            $table->foreignId('checklist_asign_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_ots');
    }
};
