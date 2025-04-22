<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\LanguageEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('telegraph_chat_language', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_id')->index()->comment('ID чата');
            $table->string('language', 2)
                ->default(LanguageEnum::RU->value)
                ->comment('Ключ выбранного языка в чате');

            $table->foreign('chat_id')
                ->references('id')
                ->on('telegraph_chats')
                ->onDelete('cascade');

            $table->comment('Таблица для хранения языка чата');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['chat_id']);
        });

        Schema::dropIfExists('telegraph_chat_language');
    }
};
