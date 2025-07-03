<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\VpnKey;
use App\Services\OutlineVpnService;
use Illuminate\Console\Command;

/**
 * Команда удаляет истекшие ключи
 *
 * @class RemoveExpiredKeys
 */
class RemoveExpiredKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove-expired-keys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удаление истекшего ключа из системы';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        VpnKey::query()
            ->select(['id', 'key_id', 'chat_id', 'expired_at'])
            ->with('chat')
            ->whereNotNull('expired_at')
            ->where('expired_at', '<', now()->toDateTimeString())
            ->orderBy('id')
            ->chunkById(100, function ($keys) {
                /** @var VpnKey $key */
                foreach ($keys as $key) {
                    try {
                        if ($key->chat === null) {
                            \Log::warning("Нет чата для ключа $key->id");
                            continue;
                        }

                        $key->chat->message(__('messages.remove_expired_keys'))->send();

                        if (new OutlineVpnService()->deleteKey((int) $key->key_id)) {
                            if (!$key->delete()) {
                                throw new \Exception("При удалении записи из бд произошла ошибка $key->id");
                            }
                        }
                    } catch (\Throwable $e) {
                        \Log::error("Ошибка при ключе $key->id: {$e->getMessage()}");
                    }
                }
            });
    }
}
