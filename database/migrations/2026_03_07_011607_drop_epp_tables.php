<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('epp_replacement_alerts');
        Schema::dropIfExists('epp_delivery_requests');
        Schema::dropIfExists('epp_delivery_items');
        Schema::dropIfExists('epp_deliveries');
        Schema::dropIfExists('epp_items');
        Schema::dropIfExists('visit_epp_deliveries');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No implementado intencionalmente para evitar restaurar la estructura anterior.
    }
};
