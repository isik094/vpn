<?php

declare(strict_types=1);

namespace App\Telegram;

use App\Enums\LanguageEnum;
use App\Models\Tariff;
use App\Services\ChatLanguageService;
use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Support\Stringable;

/**
 * @class Handler
 */
class Handler extends WebhookHandler
{
    use TelegraphTrait;

    /**
     * Стартовое сообщение и определяем язык
     *
     * @return void
     */
    public function start(): void
    {
        $currentLanguage = $this->getLanguageChatValue();
        $startMessage = $currentLanguage === null
            ? __('messages.welcome', locale: LanguageEnum::RU->value)
            : __('messages.change_language', locale: $currentLanguage);

        $ruLanguage =  __('messages.language', locale: LanguageEnum::RU->value);
        $enLanguage =  __('messages.language', locale: LanguageEnum::EN->value);

        $this->chat->message($startMessage)
            ->keyboard(
                Keyboard::make()->buttons([
                    Button::make($ruLanguage)->action('language')->param('lang', LanguageEnum::RU->value),
                    Button::make($enLanguage)->action('language')->param('lang', LanguageEnum::EN->value),
                ])
            )->send();
    }

    /**
     * Определить язык общения с пользователем
     *
     * @return void
     * @throws \Exception
     */
    public function language(): void
    {
        $chat = $this->chat;
        $lang = $this->data->get('lang');

        if (!LanguageEnum::tryFrom($lang)) {
            $chat->message('The language selected is incorrect.')->send();
            return;
        }

        if (ChatLanguageService::setLanguage($chat, $lang)) {
            $chat->message( __('messages.info_message', locale: $lang))
                ->keyboard(Keyboard::make()->buttons(Tariff::getButtons($lang)))
                ->send();
        }
    }

    /**
     * Формирование оплаты
     *
     * @return void
     */
    public function payment(): void
    {
        $chat = $this->chat;
        $countMonth = $this->data->get('count_month');
        $currentLanguage = $this->getLanguageChatValue();

        if (!Tariff::where('count_month', $countMonth)->exists()) {
            $chat->message('The count month selected is incorrect.')->send();
            return;
        }

        $chat->message(__('messages.payment', ['url' => 'https://www.ya.ru'], $currentLanguage))->send();
    }

    public function paymentCallback(): void
    {
        // todo принять платеж и чат id и выдать qr-code и файл
    }

    public function hello(string $name): void
    {
        $this->reply("Hello, world! $name");
    }

    public function help(): void
    {
        $this->reply('*Hello!* я говорить только привет');
    }

    public function buttons(): void
    {
        Telegraph::message('hello world')
            ->keyboard(
                Keyboard::make()->buttons([
                    Button::make('open')->url('https://test.it'),
                    Button::make('Web App')->action('like'),
                    Button::make('Web App')->action('test')->param('name', 'isik'),
                ])
            )->send();
    }

    public function test(): void
    {
        $name = $this->data->get('name');
        $this->reply("Hello, world! $name");
    }

    public function like(): void
    {
        Telegraph::message('Спасибо за лайк!')->send();
        $this->reply('Красава что оценил ! 👍');
    }

    protected function handleUnknownCommand(Stringable $text): void
    {
        $this->reply('Я еще не научился выполнять данную команду, предложи ее по электронноц почте isik@icloud.com');
    }

    protected function handleChatMessage(Stringable $text): void
    {
        $this->reply('Выполнял через команду, я не хочу с тобой общаться');
    }
}
