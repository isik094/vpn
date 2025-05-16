<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tariff;
use DefStudio\Telegraph\Models\TelegraphChat;

/**
 * Сервис для генерации оплаты
 *
 * @class PaymentServices
 */
class PaymentService
{
    /** @var int Сумма в рублях за один месяц */
    private const int AMOUNT_ONE_MONTH = 100;

    public function __construct(
        public TelegraphChat $chat,
        public Tariff $tariff,
    ) {
    }

    public function getPaymentUrl(): string
    {
        return 'https://www.ya.ru';
    }

    public static function markAsPaid()
    {

    }
}
