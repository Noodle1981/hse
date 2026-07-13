<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('grupo')->nullable()->after('status');
            $table->string('subgrupo')->nullable()->after('grupo');
        });

        Schema::table('asset_types', function (Blueprint $table) {
            $table->string('group_label')->nullable()->after('description');
            $table->string('subgroup_label')->nullable()->after('group_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['grupo', 'subgrupo']);
        });

        Schema::table('asset_types', function (Blueprint $table) {
            $table->dropColumn(['group_label', 'subgroup_label']);
        });
    }
};
