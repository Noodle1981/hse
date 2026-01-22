<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visit_certificates', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('end_time');
            $table->string('responsible_name')->nullable()->after('notes');
            $table->longText('responsible_signature')->nullable()->after('responsible_name');
        });
    }

    public function down(): void
    {
        Schema::table('visit_certificates', function (Blueprint $table) {
            $table->dropColumn(['notes', 'responsible_name', 'responsible_signature']);
        });
    }
};
