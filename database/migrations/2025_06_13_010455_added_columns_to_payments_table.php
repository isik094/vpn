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
            $table->uuid('external_id')
                ->unique()
                ->nullable()
                ->after('id')
                ->comment('Идентификатор заказа в системе WATA');

            $table->string('status')
                ->change()
                ->comment('Статус');

            $table->string('terminal_name')
                ->nullable()
                ->comment('Название магазина мерчанта');

            $table->uuid('terminal_public_id')
                ->nullable()
                ->comment('Идентификатор магазина мерчанта');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['external_id', 'terminal_name', 'terminal_public_id']);
        });
    }
};
