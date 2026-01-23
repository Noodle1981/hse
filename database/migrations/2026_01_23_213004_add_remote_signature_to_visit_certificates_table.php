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
            $table->text('signature_skip_reason')->nullable()->after('responsible_signature');
            $table->string('email_responsable')->nullable()->after('signature_skip_reason');
            $table->string('token_responsable')->nullable()->after('email_responsable');
            $table->string('code', 20)->nullable()->after('token_responsable');
            $table->datetime('date_firma_responsable')->nullable()->after('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visit_certificates', function (Blueprint $table) {
            $table->dropColumn([
                'signature_skip_reason',
                'email_responsable',
                'token_responsable',
                'code',
                'date_firma_responsable'
            ]);
        });
    }
};
