<?php

namespace App\Models;

use App\Enums\LanguageEnum;
use App\helpers\StrHelper;
use DefStudio\Telegraph\Keyboard\Button;
use Illuminate\Database\Eloquent\Model;

/**
 * Тарифы
 */
class Tariff extends Model
{
    /** @var bool Отключает автозаполненние временных меток */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'count_month',
    ];

    /**
     * Получить массив кнопок с активными тарифами
     *
     * @param string $lang
     * @return array
     */
    public static function getTariffButtons(string $lang): array
    {
        if (!LanguageEnum::tryFrom($lang)) {
            \Log::error('The language selected is incorrect.');
            return [];
        }

        $buttons = [];
        $monthArray = __('messages.month', [], $lang);
        $countMonths = self::where('status', true)->pluck('count_month')->toArray();

        foreach ($countMonths as $countMonth) {
            $buttonText = $countMonth . ' ' . StrHelper::declensionWord($countMonth, $monthArray);

            $buttons[] = Button::make($buttonText)
                ->action('payment')
                ->param('count_month', $countMonth);
        }

        return $buttons;
    }
}
