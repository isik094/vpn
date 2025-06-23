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
 * @property float $amount
 */
class Tariff extends Model
{
    /** @var int ID тарифа пробного периода */
    public const int IS_FREE_PERIOD_ID = 0;

    /** @var bool Отключает автозаполненние временных меток */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'count_month',
        'amount',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    /**
     * Получить массив кнопок с активными тарифами
     *
     * @param bool $isFreePeriod
     * @return array
     */
    public static function getButtons(bool $isFreePeriod = false): array
    {
        $buttons = [];
        $monthArray = __('messages.month');
        $tariffs = self::where('status', true)
            ->select('id', 'count_month', 'amount')
            ->get()
            ->toArray();

//        if ($isFreePeriod) {
//            $buttons[] = Button::make('Пробный период (3 дня)')
//                ->action('payment')
//                ->param('tariff_id', self::IS_FREE_PERIOD_ID);
//        }

        foreach ($tariffs as $tariff) {
            $buttonText = $tariff['count_month'] . ' '
                . StrHelper::declensionWord($tariff['count_month'], $monthArray)
                . " ({$tariff['amount']} ₽)";

            $buttons[] = Button::make($buttonText)
                ->action('payment')
                ->param('tariff_id', $tariff['id']);
        }

        return $buttons;
    }
}
