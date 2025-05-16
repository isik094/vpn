<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use DefStudio\Telegraph\Models\TelegraphBot;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('telegram:menu', function () {
    /** @var TelegraphBot $bot */
    $bot = TelegraphBot::find(1);

    $bot->registerCommands([
        'start' => 'Старт VPN бота, для получения инструкций',
    ])->send();
});
