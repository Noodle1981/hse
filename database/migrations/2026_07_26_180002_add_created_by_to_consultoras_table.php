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
        Schema::table('consultoras', function (Blueprint $table) {
            $table->foreignId('created_by_user_id')
                ->nullable()
                ->after('cuit')
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Usuario (vendedor o super admin) que creó la consultora');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultoras', function (Blueprint $table) {
            $table->dropForeign(['created_by_user_id']);
            $table->dropColumn('created_by_user_id');
        });
    }
};
