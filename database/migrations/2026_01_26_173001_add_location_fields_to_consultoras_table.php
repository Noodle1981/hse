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
            $table->string('province')->nullable()->after('fiscal_address');
            $table->string('locality')->nullable()->after('province');
            // Nota: Para integración futura con Países, se podría agregar 'country' o 'country_code'
            // $table->string('country_code')->default('AR')->after('locality');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultoras', function (Blueprint $table) {
            $table->dropColumn(['province', 'locality']);
        });
    }
};
