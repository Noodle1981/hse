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
        Schema::table('dynamic_checklists', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('description')->constrained();
            $table->foreignId('subcategory_id')->nullable()->after('category_id')->constrained();

            // Código de registro normativo (ej: REG-001-OT, EP-REG-008)
            $table->string('registry_code', 50)->nullable()->after('subcategory_id');

            $table->index('category_id');
            $table->index('subcategory_id');
            $table->index('registry_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dynamic_checklists', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['subcategory_id']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['subcategory_id']);
            $table->dropIndex(['registry_code']);
            $table->dropColumn(['category_id', 'subcategory_id', 'registry_code']);
        });
    }
};
