<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tariff;
use App\Models\VpnKey;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Console\Command;

/**
 * Уведомления с тарифами об оплате
 *
 * @class TariffSend
 */
class TariffSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tariff-send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отправка кнопок с выбором тарифа, для дальнейшего продления ключа Outline VPN';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $threeDaysLater = now()->addDays()->toDateTimeString();

        VpnKey::query()
            ->select(['id', 'chat_id', 'expired_at', 'send_notice'])
            ->with('chat')
            ->where('send_notice', false)
            ->whereNotNull('expired_at')
            ->where('expired_at', '<', $threeDaysLater)
            ->orderBy('id')
            ->chunkById(100, function ($keys) {
                /** @var VpnKey $key */
                foreach ($keys as $key) {
                    try {
                        if ($key->chat === null) {
                            \Log::warning("Нет чата для ключа $key->id");
                            continue;
                        }

                        $key->chat->message(__('messages.extend_key'))
                            ->keyboard(Keyboard::make()->buttons(Tariff::getButtons($key->id)))
                            ->send();

                        $key->markSendNotice();
                    } catch (\Throwable $e) {
                        \Log::error("Ошибка при ключе $key->id: {$e->getMessage()}");
                    }
                }
            });
    }
}
