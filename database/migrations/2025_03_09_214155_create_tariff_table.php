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
            $table->decimal('amount')
                ->comment('Сумма');

            $table->comment('Тарифы VPN');
        });

        \DB::table('tariffs')->insert([
            [
                'count_month' => 1,
                'status' => true,
                'amount' => 100.00
            ],
            [
                'count_month' => 3,
                'status' => true,
                'amount' => 275.00
            ],
            [
                'count_month' => 6,
                'status' => true,
                'amount' => 550.00
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tariffs');
    }
};
