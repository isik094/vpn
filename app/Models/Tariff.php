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
     * @param int|null $vpnKeyId
     * @return array
     */
    public static function getButtons(?int $vpnKeyId = null): array
    {
        $buttons = [];
        $monthArray = __('messages.month');
        $tariffs = self::where('status', true)
            ->select('id', 'count_month', 'amount')
            ->get()
            ->toArray();

        foreach ($tariffs as $tariff) {
            $buttonText = $tariff['count_month'] . ' '
                . StrHelper::declensionWord($tariff['count_month'], $monthArray)
                . " ({$tariff['amount']} ₽)";

            $action = Button::make($buttonText)
                ->action('payment')
                ->param('tariff_id', $tariff['id']);

            if ($vpnKeyId !== null) {
                $action->param('vpn_key_id', $vpnKeyId);
            }

            $buttons[] = $action;
        }

        return $buttons;
    }
}
