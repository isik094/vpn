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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('chat_id')->index()->comment('ID чата');
            $table->unsignedInteger('tariff_id')->index()->comment('ID тарифа');
            $table->string('server_name')->comment('Название сервера');
            $table->tinyInteger('status')->comment('Статус платежа');
            $table->timestamps();

            $table->foreign('chat_id')
                ->references('id')
                ->on('telegraph_chats')
                ->onDelete('cascade');

            $table->foreign('tariff_id')
                ->references('id')
                ->on('tariffs')
                ->onDelete('cascade');

            $table->comment('Платежи');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['chat_id']);
            $table->dropForeign(['tariff_id']);
        });

        Schema::dropIfExists('payments');
    }
};
