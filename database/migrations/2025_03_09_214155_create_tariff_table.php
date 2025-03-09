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
        Schema::create('tariffs', function (Blueprint $table) {
            $table->id();
            $table->integer('count_month')
                ->unsigned()
                ->comment('Количество месяцев');
            $table->boolean('status')
                ->default(false)
                ->comment('Статус');

            $table->comment('Тарифы VPN');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tariff');
    }
};
