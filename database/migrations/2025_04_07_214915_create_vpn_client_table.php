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
        Schema::create('vpn_clients', function (Blueprint $table) {
            $table->id()->comment('Уникальный идентификатор');
            $table->foreignId('telegraph_chat_id')
                ->index()
                ->comment('ID телеграмм чата')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('private_key')->comment('Приватный ключ');
            $table->string('public_key')->unique()->comment('Публичный ключ');
            $table->string('ip_address')->unique()->comment('IP адрес');
            $table->string('status')->comment('Статус')->default('active');
            $table->string('config_path')->comment('Путь до конфига');
            $table->string('qr_path')->comment('Путь до QR-кода');
            $table->timestamps();

            $table->comment('Список wg конфигов пользователей');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vpn_clients', function (Blueprint $table) {
            $table->dropForeign(['telegraph_chat_id']);
        });

        Schema::dropIfExists('vpn_client');
    }
};
