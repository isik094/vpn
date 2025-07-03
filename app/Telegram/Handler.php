<?php

declare(strict_types=1);

namespace App\Telegram;

use App\helpers\StrHelper;
use App\Models\Tariff;
use App\Models\VpnKey;
use App\Services\PaymentService;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Keyboard;
use GuzzleHttp\Exception\GuzzleException;
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
     * @throws GuzzleException
     * @throws \Exception
     */
    public function payment(): void
    {
        $chat = $this->chat;
        $tariffId = $this->data->get('tariff_id');
        $vpnKeyId = StrHelper::isEmpty($this->data->get('vpn_key_id'))
            ? null
            : (int) $this->data->get('vpn_key_id');

        $tariff = Tariff::where('id', $tariffId)
            ->select('id', 'count_month', 'amount')
            ->where('status', true)
            ->first();

        if ($tariff === null) {
            $chat->message('Ошибка, выбран неверный тариф.')->send();
            return;
        }

        if ($vpnKeyId !== null && !VpnKey::where('id', $vpnKeyId)->exists()) {
            $chat->message('Ошибка, задан неверный ключ при продлении тарифа, обратитесь к поддержку.')->send();
        }

        $paymentService = new PaymentService($chat, $tariff, $vpnKeyId);
        $wataServiceData = $paymentService->create();

        $this->chat->message(__('messages.pay_text'))
            ->keyboard(
                Keyboard::make()
                    ->button('🏦 СБП')
                    ->url($paymentService->getUrl($wataServiceData))
            )
            ->send();
    }

    /**
     * Техническая поддержка
     *
     * @return void
     */
    public function support(): void
    {
        $this->chat->message(__('messages.support'))->send();
    }

    /**
     * Список ключей пользователя
     *
     * @return void
     */
    public function keys(): void
    {
        $chat = $this->chat;
        $chat->message(VpnKey::listKey($chat))
            ->markdownV2()
            ->send();
    }

    /**
     * Правила использования VPN
     *
     * @return void
     */
    public function policy(): void
    {
        $this->chat->message(__('messages.policy'))->send();
    }

    /**
     * Политика конфиденциальности
     *
     * @return void
     */
    public function privacy(): void
    {
        $this->chat->message(__('messages.privacy'))->send();
    }

    /**
     * Обработка неизвестных команд
     *
     * @param Stringable $text
     * @return void
     */
    public function handleUnknownCommand(Stringable $text): void
    {
        $response = "🤖 *Ой-ой!*\n\n"
            . "Мой код не содержит команды `" . $text . "`\n\n"
            . "Давайте лучше выберем что-то из *меню*.";

        $this->chat->message($response)->send();

        \Log::warning("Unknown command", [
            'user' => $this->chat->id,
            'message' => (string)$text
        ]);
    }

    /**
     * Обработка набранного текста в чате
     *
     * @param Stringable $text
     * @return void
     */
    protected function handleChatMessage(Stringable $text): void
    {
        if ($text->startsWith('/')) {
            $this->handleUnknownCommand($text);
            return;
        }

        $response = "📝 *Вы прислали текст:*\n\"$text\"\n\n"
            . "Я бот для работы с VPN ключами и не понимаю произвольные сообщения.\n\n"
            . "Пожалуйста, используйте команды из *меню*.";

        $this->chat->message($response)->send();
    }
}
