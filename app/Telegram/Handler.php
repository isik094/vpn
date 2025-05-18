<?php

declare(strict_types=1);

namespace App\Telegram;

use App\Models\Tariff;
use App\Services\PaymentServiceFreeKassa;
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
        $tariff = Tariff::where('count_month', $countMonth)
            ->where('status', true)
            ->select('id', 'count_month')
            ->first();

        if ($tariff === null) {
            $chat->message('Ошибка, выбран неверный тариф.')->send();
            return;
        }

        $paymentServices = new PaymentServiceFreeKassa($chat, $tariff);
        $paymentUrl = $paymentServices->getPaymentUrl();

        $chat->message("Оплата за выбранный тариф")
            ->keyboard(function (Keyboard $keyboard) use ($paymentUrl) {
                return $keyboard->button('💳 Оплатить')->url($paymentUrl);
            })
            ->send();

//        $chat->message(__('messages.payment', ['url' => $paymentServices->getPaymentUrl()]))->send();
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
