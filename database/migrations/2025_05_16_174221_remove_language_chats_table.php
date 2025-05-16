<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('telegraph_chat_language');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
