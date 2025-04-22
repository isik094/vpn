<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $public_key
 * @property string $ip_address
 * @property string $config_path
 * @property string $qr_path
 */
class VpnClient extends Model
{
    protected $fillable = [
        'telegraph_chat_id',
        'private_key',
        'public_key',
        'ip_address',
        'status',
        'config_path',
        'qr_path'
    ];

    /**
     * Получить чат телеграмм
     *
     * @return BelongsTo
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(TelegraphChat::class);
    }
}
