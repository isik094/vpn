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
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedInteger('vpn_key_id')->index()->comment('ID vpn ключа');
            $table->unsignedInteger('server_id')->index()->comment('ID сервера');

            $table->foreign('vpn_key_id')
                ->references('id')
                ->on('vpn_keys')
                ->onDelete('cascade');

            $table->foreign('server_id')
                ->references('id')
                ->on('servers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['vpn_key_id', 'server_id']);
            $table->dropColumn(['vpn_key_id', 'server_id']);
        });
    }
};
