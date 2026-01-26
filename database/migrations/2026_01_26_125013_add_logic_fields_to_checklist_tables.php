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
        Schema::table('dynamic_questions', function (Blueprint $table) {
            $table->decimal('min_value', 10, 2)->nullable()->after('options');
            $table->decimal('max_value', 10, 2)->nullable()->after('min_value');
            $table->json('failure_values')->nullable()->after('max_value');
        });

        Schema::table('dynamic_inspections', function (Blueprint $table) {
            $table->string('result')->nullable()->after('status'); // Status could be 'Pass', 'Fail', or 'Draft'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dynamic_questions', function (Blueprint $table) {
            $table->dropColumn(['min_value', 'max_value', 'failure_values']);
        });

        Schema::table('dynamic_inspections', function (Blueprint $table) {
            $table->dropColumn('result');
        });
    }
};
