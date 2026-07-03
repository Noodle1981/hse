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
        Schema::table('asset_types', function (Blueprint $table) {
            if (! Schema::hasColumn('asset_types', 'photo_schema')) {
                $table->json('photo_schema')->nullable()->after('metadata_schema');
            }
        });

        Schema::table('assets', function (Blueprint $table) {
            if (! Schema::hasColumn('assets', 'photos')) {
                $table->json('photos')->nullable()->after('metadata');
            }
        });

        Schema::table('incidentes', function (Blueprint $table) {
            if (! Schema::hasColumn('incidentes', 'asset_id')) {
                $table->foreignUuid('asset_id')->nullable()->after('sede_id')->constrained('assets')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_types', function (Blueprint $table) {
            if (Schema::hasColumn('asset_types', 'photo_schema')) {
                $table->dropColumn('photo_schema');
            }
        });

        Schema::table('assets', function (Blueprint $table) {
            if (Schema::hasColumn('assets', 'photos')) {
                $table->dropColumn('photos');
            }
        });

        Schema::table('incidentes', function (Blueprint $table) {
            if (Schema::hasColumn('incidentes', 'asset_id')) {
                $table->dropForeign(['asset_id']);
                $table->dropColumn('asset_id');
            }
        });
    }
};
