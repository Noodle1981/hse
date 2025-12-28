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
        Schema::table('dynamic_inspections', function (Blueprint $table) {
            $table->foreignId('sede_id')->after('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('checklist_assignment_id')->after('dynamic_checklist_id')->nullable()->constrained('checklist_assignments')->nullOnDelete();
            $table->string('supervisor_name')->nullable()->after('status');
            $table->text('supervisor_signature')->nullable()->after('supervisor_name');
            $table->text('technician_signature')->nullable()->after('supervisor_signature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dynamic_inspections', function (Blueprint $table) {
            $table->dropForeign(['sede_id']);
            $table->dropForeign(['checklist_assignment_id']);
            $table->dropColumn(['sede_id', 'checklist_assignment_id', 'supervisor_name', 'supervisor_signature', 'technician_signature']);
        });
    }
};
