<?php

namespace App\Models;

use App\helpers\StrHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use DefStudio\Telegraph\Models\TelegraphChat;

/**
 * App\Models\VpnKey
 *
 * @property int $id
 * @property int $chat_id
 * @property string $key_id
 * @property string $name
 * @property string $password
 * @property int $port
 * @property string $method
 * @property string $accessUrl
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $expired_at
 * @property bool $send_notice
 *
 * @property TelegraphChat $chat
 */
class VpnKey extends Model
{
    protected $fillable = [
        'chat_id',
        'key_id',
        'name',
        'password',
        'port',
        'method',
        'accessUrl',
        'created_at',
        'expired_at',
        'send_notice',
    ];

    /**
     * Получить чат
     *
     * @return BelongsTo
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(TelegraphChat::class, 'chat_id');
    }

    /**
     * Получить все платежи ключа
     *
     * @return HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Получить список ключей пользователя в скрытом виде
     *
     * @param TelegraphChat $chat
     * @return string
     */
    public static function listKey(TelegraphChat $chat): string
    {
        $text = __('messages.keys');
        $keys = self::where('chat_id', $chat->id)->get();

        if ($keys->isEmpty()) {
            $text .= '||*пусто* 🙅🏻‍♂️||';
            return $text;
        }

        $text .= PHP_EOL . PHP_EOL;
        foreach ($keys as $index => $key) {
            $expiredAt = Carbon::parse($key->expired_at)->format('d.m.Y');

            $text .= ($index + 1) . '\\) ||' . StrHelper::escapedText($key->accessUrl) . '||' . PHP_EOL;
            $text .= '⏳ Истекает: ' . StrHelper::escapedText($expiredAt) . PHP_EOL . PHP_EOL;
        }

        return $text;
    }

    /**
     * Задать время окончания ключа
     *
     * @param Tariff $tariff
     * @param string $date
     * @return void
     */
    public function setExpiredAt(Tariff $tariff, string $date): void
    {
        $this->expired_at = Carbon::parse($date)->addMonths($tariff->count_month);
    }

    /**
     * Отметить отправку уведомления с тарифами для продления ключа
     *
     * @return void
     * @throws \Exception
     */
    public function markSendNotice(): void
    {
        $this->send_notice = true;

        if (!$this->save()) {
            throw new \Exception("При сохранении записи произошла ошибка $this->id");
        }
    }
}
