<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LanguageEnum;
use DefStudio\Telegraph\Models\TelegraphChat;
use App\Models\TelegraphChatLanguage;

/**
 * @class ChatLanguageService
 */
class ChatLanguageService
{
    /**
     * @param TelegraphChat $chat
     * @param string $lang
     * @return bool
     */
    public static function setLanguage(TelegraphChat $chat, string $lang): bool
    {
        if (!LanguageEnum::tryFrom($lang)) {
            return false;
        }

        try {
            TelegraphChatLanguage::updateOrCreate(
                ['chat_id' => $chat->id],
                ['language' => $lang]
            );

            return true;
        } catch (\Exception $e) {
            \Log::error('Language update failed: ' . $e->getMessage());
            return false;
        }
    }
}
