<?php

declare(strict_types=1);

namespace App\Telegram;

use App\Models\TelegraphChatLanguage;

/**
 * Общие методы для телеграмма VPN
 *
 * Trait MainTelegramTrait
 */
trait TelegraphTrait
{
    /**
     * Получить значение языка чата
     *
     * @return string|null
     */
    public function getLanguageChatValue(): ?string
    {
        return TelegraphChatLanguage::where('chat_id', $this->chat->id)->value('language');
    }

    /**
     * @return TelegraphChatLanguage|null
     */
    public function getLanguageChat(): ?TelegraphChatLanguage
    {
        return TelegraphChatLanguage::where('chat_id', $this->chat->id)->first();
    }
}
