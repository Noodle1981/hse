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
        Schema::create('annual_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained()->onDelete('cascade');
            $table->string('category'); // Capacitación, Inspección, Registro
            $table->string('activity_name');
            $table->string('activity_code')->nullable();
            $table->foreignId('checklist_id')->nullable()->constrained('dynamic_checklists')->onDelete('set null');
            $table->integer('year')->default(date('Y'));

            // Meses Planificados (las "X")
            $table->boolean('january')->default(false);
            $table->boolean('february')->default(false);
            $table->boolean('march')->default(false);
            $table->boolean('april')->default(false);
            $table->boolean('may')->default(false);
            $table->boolean('june')->default(false);
            $table->boolean('july')->default(false);
            $table->boolean('august')->default(false);
            $table->boolean('september')->default(false);
            $table->boolean('october')->default(false);
            $table->boolean('november')->default(false);
            $table->boolean('december')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annual_plans');
    }
};
