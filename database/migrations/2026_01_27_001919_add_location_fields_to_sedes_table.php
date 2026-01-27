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
        Schema::table('sedes', function (Blueprint $table) {
            $table->foreignId('provincia_id')->nullable()->constrained('provincias')->nullOnDelete()->after('localidad');
            $table->decimal('latitud', 10, 8)->nullable()->after('provincia_id');
            $table->decimal('longitud', 11, 8)->nullable()->after('latitud');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sedes', function (Blueprint $table) {
            $table->dropForeign(['provincia_id']);
            $table->dropColumn(['provincia_id', 'latitud', 'longitud']);
        });
    }
};
