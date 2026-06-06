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
            $table->foreignId('section_id')->nullable()->after('dynamic_checklist_id')->constrained('checklist_sections')->nullOnDelete();

            $table->index('section_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dynamic_questions', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropIndex(['section_id']);
            $table->dropColumn('section_id');
        });
    }
};
