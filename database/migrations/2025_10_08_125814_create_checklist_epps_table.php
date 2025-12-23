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
        Schema::create('checklist_epps', function (Blueprint $table) {
            $table->id();
            $table->string('inspector')->nullable();
            $table->string('establecimiento')->nullable();
            $table->string('area')->nullable();
            $table->string('responsable')->nullable();
            $table->boolean('pa1_aplica')->nullable();
            $table->boolean('pa1_requerido')->nullable();
            $table->string('pa1_estado', length: 9)->nullable();
            $table->boolean('pa2_aplica')->nullable();
            $table->boolean('pa2_requerido')->nullable();
            $table->string('pa2_estado', length: 9)->nullable();
            $table->boolean('oc1_aplica')->nullable();
            $table->boolean('oc1_requerido')->nullable();
            $table->string('oc1_estado', length: 9)->nullable();
            $table->boolean('oc2_aplica')->nullable();
            $table->boolean('oc2_requerido')->nullable();
            $table->string('oc2_estado', length: 9)->nullable();
            $table->boolean('oc3_aplica')->nullable();
            $table->boolean('oc3_requerido')->nullable();
            $table->string('oc3_estado', length: 9)->nullable();
            $table->boolean('oc4_aplica')->nullable();
            $table->boolean('oc4_requerido')->nullable();
            $table->string('oc4_estado', length: 9)->nullable();
            $table->boolean('oc5_aplica')->nullable();
            $table->boolean('oc5_requerido')->nullable();
            $table->string('oc5_estado', length: 9)->nullable();
            $table->boolean('nb1_aplica')->nullable();
            $table->boolean('nb1_requerido')->nullable();
            $table->string('nb1_estado', length: 9)->nullable();
            $table->boolean('ma1_aplica')->nullable();
            $table->boolean('ma1_requerido')->nullable();
            $table->string('ma1_estado', length: 9)->nullable();
            $table->boolean('ma2_aplica')->nullable();
            $table->boolean('ma2_requerido')->nullable();
            $table->string('ma2_estado', length: 9)->nullable();
            $table->boolean('ma3_aplica')->nullable();
            $table->boolean('ma3_requerido')->nullable();   
            $table->string('ma3_estado', length: 9)->nullable();
            $table->boolean('ma4_aplica')->nullable();
            $table->boolean('ma4_requerido')->nullable();
            $table->string('ma4_estado', length: 9)->nullable();
            $table->boolean('ma5_aplica')->nullable();
            $table->boolean('ma5_requerido')->nullable();
            $table->string('ma5_estado', length: 9)->nullable();
            $table->boolean('ma6_aplica')->nullable();
            $table->boolean('ma6_requerido')->nullable();
            $table->string('ma6_estado', length: 9)->nullable();
            $table->boolean('ma7_aplica')->nullable();
            $table->boolean('ma7_requerido')->nullable();
            $table->string('ma7_estado', length: 9)->nullable();
            $table->boolean('ma8_aplica')->nullable();
            $table->boolean('ma8_requerido')->nullable();
            $table->string('ma8_estado', length: 9)->nullable();
            $table->boolean('pp1_aplica')->nullable();
            $table->boolean('pp1_requerido')->nullable();
            $table->string('pp1_estado', length: 9)->nullable();
            $table->boolean('pp2_aplica')->nullable();
            $table->boolean('pp2_requerido')->nullable();
            $table->string('pp2_estado', length: 9)->nullable();
            $table->boolean('pp3_aplica')->nullable();
            $table->boolean('pp3_requerido')->nullable();
            $table->string('pp3_estado', length: 9)->nullable();
            $table->boolean('cu1_aplica')->nullable();
            $table->boolean('cu1_requerido')->nullable();
            $table->string('cu1_estado', length: 9)->nullable();
            $table->boolean('cu2_aplica')->nullable();
            $table->boolean('cu2_requerido')->nullable();
            $table->string('cu2_estado', length: 9)->nullable();
            $table->boolean('cu3_aplica')->nullable();
            $table->boolean('cu3_requerido')->nullable();
            $table->string('cu3_estado', length: 9)->nullable();
            $table->boolean('cu4_aplica')->nullable();
            $table->boolean('cu4_requerido')->nullable();
            $table->string('cu4_estado', length: 9)->nullable();
            $table->boolean('ca1_aplica')->nullable();
            $table->boolean('ca1_requerido')->nullable();
            $table->string('ca1_estado', length: 9)->nullable();
            $table->boolean('ca2_aplica')->nullable();
            $table->boolean('ca2_requerido')->nullable();
            $table->string('ca2_estado', length: 9)->nullable();
            $table->boolean('ca3_aplica')->nullable();
            $table->boolean('ca3_requerido')->nullable();
            $table->string('ca3_estado', length: 9)->nullable();
            $table->boolean('tr1_aplica')->nullable();
            $table->boolean('tr1_requerido')->nullable();
            $table->string('tr1_estado', length: 9)->nullable();
            $table->boolean('tr2_aplica')->nullable();
            $table->boolean('tr2_requerido')->nullable();
            $table->string('tr2_estado', length: 9)->nullable();
            $table->text('acciones')->nullable();
            $table->foreignId('checklist_asign_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_epps');
    }
};
