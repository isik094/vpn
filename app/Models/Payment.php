<?php

namespace App\Models;

use App\Enums\PaymentStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Payment
 *
 * @property int $id
 * @property int $chat_id
 * @property int $tariff_id
 * @property string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $vpn_key_id
 * @property int $server_id
 * @property string $external_id
 * @property string $terminal_name
 * @property string $terminal_public_id
 */
class Payment extends Model
{
    protected $fillable = [
        'chat_id',
        'tariff_id',
        'status',
        'created_at',
        'vpn_key_id',
        'server_id',
        'external_id',
        'terminal_name',
        'terminal_public_id',
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
     * Получить тариф
     *
     * @return BelongsTo
     */
    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class, 'tariff_id');
    }

    /**
     * Получить сервер
     *
     * @return BelongsTo
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class, 'server_id');
    }

    /**
     * Получить ключ
     *
     * @return BelongsTo
     */
    public function vpnKey(): BelongsTo
    {
        return $this->belongsTo(VpnKey::class, 'vpn_key_id');
    }

    /**
     * Задать статус
     *
     * @param PaymentStatusEnum $status
     * @return void
     */
    public function setStatus(PaymentStatusEnum $status): void
    {
        $this->status = $status->value;
    }
}
