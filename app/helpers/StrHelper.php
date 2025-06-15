<?php

declare(strict_types=1);

namespace App\helpers;

use Illuminate\Support\Str;

/**
 * Хелпер для работы со строками
 */
class StrHelper extends Str
{
    /**
     * Проверить строку на пустоту
     *
     * @param string|null $value
     * @return bool
     */
    public static function isEmpty(?string $value): bool
    {
        if ($value === null || trim($value) === '') {
            return true;
        }

        return false;
    }

    /**
     * Склонение слова в зависимости от числа
     *
     * @param int $number - число, в зависимости от которого склоняем
     * @param array $forms - массив склоненных слов
     * @return string
     */
    public static function declensionWord(int $number, array $forms): string
    {
        if (count($forms) !== 3) {
            throw new \InvalidArgumentException('Pass an array of three forms of the word.');
        }

        if ($number % 10 == 1 && $number % 100 != 11) {
            return $forms[0];
        } elseif ($number % 10 >= 2 && $number % 10 <= 4 && ($number % 100 < 10 || $number % 100 >= 20)) {
            return $forms[1];
        } else {
            return $forms[2];
        }
    }

    /**
     * Экранируем спецсимволы для MarkdownV2
     *
     * @param string $text
     * @return string
     */
    public static function escapedText(string $text): string
    {
        return str_replace(
            ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'],
            ['\_', '\*', '\[', '\]', '\(', '\)', '\~', '\`', '\>', '\#', '\+', '\-', '\=', '\|', '\{', '\}', '\.', '\!'],
            $text
        );
    }
}
