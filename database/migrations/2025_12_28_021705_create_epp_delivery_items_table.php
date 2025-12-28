<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('epp_delivery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('epp_delivery_id')->constrained()->onDelete('cascade');
            $table->foreignId('epp_item_id')->constrained();
            $table->integer('cantidad')->default(1);
            $table->enum('tipo_entrega', ['inicial', 'reposicion_rotura', 'reposicion_fecha'])->default('inicial');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('epp_delivery_items');
    }
};
