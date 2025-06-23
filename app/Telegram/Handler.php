<?php

declare(strict_types=1);

namespace App\Telegram;

use App\Models\Tariff;
use App\Models\VpnKey;
use App\Services\OutlineVpnService;
use App\Services\PaymentService;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Button;
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

        $tariff = Tariff::where('id', $tariffId)
            ->where('status', true)
            ->select('id', 'count_month', 'amount')
            ->first();

        if ($tariff === null) {
            $chat->message('Ошибка, выбран неверный тариф.')->send();
            return;
        }

        // TODO Временно пока не работает платежный сервис
        if ((int) $tariff->id === 4) {
            $outlineVpnService = new OutlineVpnService();
            $vpnKey = $outlineVpnService->createKey($this->chat->id);

            if ($vpnKey === null) {
                logger()->error('Failed to create key');
                throw new \Exception("Failed to create key");
            }

            $currentDateTime = date('Y-m-d H:i:s');
            $vpnKey->setExpiredAt($tariff, $currentDateTime);

            if (!$vpnKey->save()) {
                logger()->error('Failed to save vpn');
                throw new \Exception("Failed to save vpn");
            }

            $message = $outlineVpnService->getMessage($vpnKey->accessUrl, $currentDateTime, 0);
            $this->chat->message($message)->send();
        } else {
            $paymentService = new PaymentService($chat, $tariff);
            $wataServiceData = $paymentService->create();

            $this->chat->message(__('messages.pay_text'))
                ->keyboard(
                    Keyboard::make()
                        ->button('💳 Оплатить')
                        ->webApp($paymentService->getUrl($wataServiceData))
                )
                ->send();
        }
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
     * Обработка неизвестных команд
     *
     * @param Stringable $text
     * @return void
     */
    public function handleUnknownCommand(Stringable $text): void
    {
        $response = "🤖 *Ой-ой!*\n\n"
            . "Мой код не содержит команды `" . $text . "`\n\n"
            . "Давайте лучше выберем что-то из *списка ниже*:";

        $this->chat->message($response)
            ->keyboard(Keyboard::make()->buttons([
                Button::make('▶️ Старт')->action('start'),
                Button::make('🔑 Мои ключи')->action('keys'),
                Button::make('📜 Правила')->action('policy'),
                Button::make('🆘 Поддержка')->action('support'),
            ]))
            ->send();

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
            . "Пожалуйста, используйте команды из меню.";

        $this->chat->message($response)->send();
    }
}
