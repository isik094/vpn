<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use DefStudio\Telegraph\Models\TelegraphBot;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('telegraph:menu', function () {
    /** @var TelegraphBot $bot */
    $bot = TelegraphBot::find(1);

    $bot->registerCommands([
        'start' => '🚀 Старт',
        'keys' => '🔑 Мои ключи',
        'support' => '💬 Тех. поддержка',
        'policy' => '📜 Политика использования',
        'privacy' => '🔐 Политика конфиденциальности',
    ])->send();
});

Schedule::command('tariff-send')->hourly();
Schedule::command('remove-expired-keys')->hourly();
