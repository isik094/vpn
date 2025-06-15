<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Server
 *
 * @property int $id
 * @property string $location
 * @property string $ip
 * @property int $max_connect
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Server extends Model
{
    protected $fillable = [
        'location',
        'ip',
        'max_connect',
        'created_at'
    ];

    /**
     * Получить платежи для сервера
     *
     * @return HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Вернуть любой id сервера
     *
     * @return int
     */
    public static function getServerId(): int
    {
        return Server::select('id')->limit(1)->value('id');
    }
}
