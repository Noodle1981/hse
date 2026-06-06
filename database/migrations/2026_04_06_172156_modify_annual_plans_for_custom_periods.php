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
        Schema::table('annual_plans', function (Blueprint $table) {
            $table->dropColumn([
                'year', 'january', 'february', 'march', 'april', 'may', 'june',
                'july', 'august', 'september', 'october', 'november', 'december',
            ]);
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->json('scheduled_months')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('annual_plans', function (Blueprint $table) {
            $table->dropColumn(['period_start', 'period_end', 'scheduled_months']);
            $table->integer('year')->default(date('Y'));
            $table->boolean('january')->default(false);
            $table->boolean('february')->default(false);
            $table->boolean('march')->default(false);
            $table->boolean('april')->default(false);
            $table->boolean('may')->default(false);
            $table->boolean('june')->default(false);
            $table->boolean('july')->default(false);
            $table->boolean('august')->default(false);
            $table->boolean('september')->default(false);
            $table->boolean('october')->default(false);
            $table->boolean('november')->default(false);
            $table->boolean('december')->default(false);
        });
    }
};
