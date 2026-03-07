<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultoras', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('config');
            $table->string('color_primary')->default('#1e40af')->after('logo_path');
            $table->string('color_secondary')->default('#6b7280')->after('color_primary');
            $table->string('color_accent')->default('#16a34a')->after('color_secondary');
        });
    }

    public function down(): void
    {
        Schema::table('consultoras', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'color_primary', 'color_secondary', 'color_accent']);
        });
    }
};
