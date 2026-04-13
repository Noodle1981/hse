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
        Schema::table('visit_certificates', function (Blueprint $table) {
            $table->timestamp('token_expires_at')->nullable()->after('token_responsable');
            $table->boolean('token_used')->default(false)->after('token_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visit_certificates', function (Blueprint $table) {
            //
        });
    }
};
