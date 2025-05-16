<?php

declare(strict_types=1);

namespace App\Telegram;

use App\Models\Tariff;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Support\Stringable;

/**
 * @class Handler
 */
class Handler extends WebhookHandler
{
    /**
     * Стартовое сообщение и определяем выбор тарифа пользователя
     *
     * @return void
     */
    public function start(): void
    {
        $this->chat->message(__('messages.welcome'))
            ->keyboard(Keyboard::make()->buttons(Tariff::getButtons()))
            ->send();
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

        if (!Tariff::where('count_month', $countMonth)->exists()) {
            $chat->message('The count month selected is incorrect.')->send();
            return;
        }

        $chat->message(__('messages.payment', ['url' => 'https://www.ya.ru']))->send();
    }

    /**
     * Обработка неизвестных команд
     *
     * @param Stringable $text
     * @return void
     */
    protected function handleUnknownCommand(Stringable $text): void
    {
        $this->reply('Я еще не научился выполнять данную команду, предложи ее по электронноц почте');
    }

    /**
     * Обработка набранного текста в чате
     *
     * @param Stringable $text
     * @return void
     */
    protected function handleChatMessage(Stringable $text): void
    {
        $this->reply('Выполнял через команду, я не хочу с тобой общаться');
    }
}
