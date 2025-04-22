<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('telegram:menu', function () {
    /** @var \DefStudio\Telegraph\Models\TelegraphBot $bot */
    $bot = \DefStudio\Telegraph\Models\TelegraphBot::find(1);

    $bot->registerCommands([
        'start' => 'Выберите язык общения с ботом',
        'hello' => 'Говорить привет!',
        'help' => 'поможет тебе!',
        'buttons' => 'тест кнопок',
    ])->send();
});
