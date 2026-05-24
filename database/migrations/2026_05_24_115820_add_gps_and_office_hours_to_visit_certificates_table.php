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
            $table->decimal('horas_gabinete_estimadas', 5, 2)->nullable();
            $table->decimal('checkin_lat', 10, 8)->nullable();
            $table->decimal('checkin_lng', 11, 8)->nullable();
            $table->decimal('checkout_lat', 10, 8)->nullable();
            $table->decimal('checkout_lng', 11, 8)->nullable();
            $table->boolean('gps_verified')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visit_certificates', function (Blueprint $table) {
            $table->dropColumn([
                'horas_gabinete_estimadas',
                'checkin_lat',
                'checkin_lng',
                'checkout_lat',
                'checkout_lng',
                'gps_verified'
            ]);
        });
    }
};
