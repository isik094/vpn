<?php

declare(strict_types=1);

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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('location')->comment('Локация');
            $table->string('ip')->comment('IP сервера');
            $table->integer('max_connect')->comment('Максимальное количество соединений');
            $table->timestamps();

            $table->comment('Доступные сервера');
        });

        \DB::table('servers')->insert([
            [
                'location' => 'Paris',
                'ip' => '81.19.137.174',
                'max_connect' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
