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
            $table->text('description')->nullable()->after('nombre');
            $table->string('fiscal_address')->nullable()->after('cuit');
            $table->string('email')->nullable()->after('fiscal_address');
            $table->string('phone')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultoras', function (Blueprint $table) {
            $table->dropColumn(['description', 'fiscal_address', 'email', 'phone']);
        });
    }
};
