<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @class TelegraphChatLanguage
 *
 * @property int $id
 * @property int $chat_id
 * @property string $language
 * @property-read TelegraphChat $chat
 */
class TelegraphChatLanguage extends Model
{
    /** @var string Название таблицы */
    protected $table = 'telegraph_chat_language';

    /** @var bool Отключает автозаполненние временных меток */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'chat_id',
        'language',
    ];

    /**
     * Вернуть чат
     *
     * @return BelongsTo
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(TelegraphChat::class, 'chat_id', 'id');
    }
}
