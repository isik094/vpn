<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Payment
 *
 * @property int $id
 * @property int $chat_id
 * @property int $tariff_id
 * @property string $server_name
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Payment extends Model
{
    protected $fillable = [
        'chat_id',
        'tariff_id',
        'server_name',
        'status'
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
}
