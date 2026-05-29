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
        Schema::table('training_attendances', function (Blueprint $table) {
            $table->unsignedBigInteger('training_id')->nullable()->change();
            
            $table->foreignId('visit_training_id')
                ->nullable()
                ->after('training_id')
                ->constrained('visit_trainings')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_attendances', function (Blueprint $table) {
            $table->dropForeign(['visit_training_id']);
            $table->dropColumn('visit_training_id');
            $table->unsignedBigInteger('training_id')->nullable(false)->change();
        });
    }
};
