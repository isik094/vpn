<?php

declare(strict_types=1);

namespace App\Models;

use App\helpers\StrHelper;
use DefStudio\Telegraph\Keyboard\Button;
use Illuminate\Database\Eloquent\Model;

/**
 * Тарифы
 *
 * @property int $id
 * @property int $count_month
 * @property bool $status
 */
class Tariff extends Model
{
    /** @var bool Отключает автозаполненние временных меток */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'count_month',
    ];

    /**
     * Получить массив кнопок с активными тарифами
     *
     * @return array
     */
    public static function getButtons(): array
    {
        $buttons = [];
        $monthArray = __('messages.month');
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
