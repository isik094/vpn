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
        Schema::table('vpn_keys', function (Blueprint $table) {
            $table->boolean('send_notice')
                ->default(false)
                ->comment('Флаг отправленного уведомления об оплате');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vpn_keys', function (Blueprint $table) {
            $table->dropColumn('send_notice');
        });
    }
};
