<?php

declare(strict_types=1);

namespace App\Enums;

enum LanguageEnum: string
{
    case RU = 'ru';
    case EN = 'en';

    /**
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::RU => 'Русский',
            self::EN => 'English',
        };
    }
}
