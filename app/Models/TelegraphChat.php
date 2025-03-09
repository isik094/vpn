<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read TelegraphChatLanguage $language
 */
class TelegraphChat extends \DefStudio\Telegraph\Models\TelegraphChat
{
    /**
     * Получить язык
     *
     * @return HasOne
     */
    public function language(): HasOne
    {
        return $this->hasOne(TelegraphChatLanguage::class, 'chat_id', 'id');
    }
}
