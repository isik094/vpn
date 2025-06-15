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
        Schema::create('vpn_keys', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('chat_id')->index()->comment('ID чата');
            $table->string('key_id')
                ->unique()
                ->index()
                ->comment('ID ключа в Outline VPN');
            $table->string('name')->comment('Название ключа');
            $table->string('password')->comment('Пароль');
            $table->integer('port')->comment('Порт');
            $table->string('method')->comment('Метод');
            $table->string('accessUrl')->comment('Ссылка доступа к VPN');
            $table->timestamps();

            $table->foreign('chat_id')
                ->references('id')
                ->on('telegraph_chats')
                ->onDelete('cascade');

            $table->comment('Ключи VPN');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vpn_keys');
    }
};
