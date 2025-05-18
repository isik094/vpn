<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PaymentStatusEnum;
use App\Models\Payment;
use App\Models\Tariff;
use DefStudio\Telegraph\Models\TelegraphChat;
use League\Config\Exception\InvalidConfigurationException;

/**
 * Сервис для генерации оплаты FreeKassa
 *
 * @class PaymentServices
 */
class PaymentServiceFreeKassa
{
    /** @var float Сумма в рублях за один месяц */
    private const float AMOUNT_ONE_MONTH = 100.00;

    /** @var string Валюта оплаты */
    private const string CURRENCY = 'RUB';

    /** @var int Способо оплаты по умолчанию СБП */
    private const int DEFAULT_PAYMENT_METHOD = 42;

    /** @var string Язык по умолчанию */
    private const string DEFAULT_LANGUAGE = 'ru';

    /** @var string Базовая ссылка на оплату FreeKassa */
    private string $paymentBaseUrl = 'https://pay.fk.money/';

    public function __construct(
        public TelegraphChat $chat,
        public Tariff $tariff,
    ) {
    }

    /**
     * Получить сформированную ссылку на оплату
     *
     * @return string
     */
    public function getPaymentUrl(): string
    {
        return $this->paymentBaseUrl . http_build_query($this->getParams());
    }

    /**
     * Сформировать параметры для оплаты
     *
     * @return array
     */
    public function getParams(): array
    {
        $merchantId = config('freekassa.merchant_id');
        $secretKeyOne = config('freekassa.secret_key_1');

        if ($merchantId === null || $secretKeyOne === null) {
            throw new InvalidConfigurationException('Задайте данные для FreeKassa в конфигурации приложения');
        }

        $payment = $this->getPayment();
        $amount = self::AMOUNT_ONE_MONTH * $this->tariff->count_month;
        $currency = self::CURRENCY;

        return [
            'm' => $merchantId,
            'oa' => $amount,
            'currency' => $currency,
            'o' => $payment->id,
            's' => md5("$merchantId:$amount:$secretKeyOne:$currency:$payment->id"),
            'i' => self::DEFAULT_PAYMENT_METHOD,
            'lang' => self::DEFAULT_LANGUAGE,
            'us_chat_id' => $this->chat->id,
            'us_tariff_id' => $this->tariff->id,
        ];
    }

    private function getPayment(): Payment
    {
        $payment = new Payment();
        $payment->chat_id = $this->chat->id;
        $payment->tariff_id = $this->tariff->id;
        $payment->status = PaymentStatusEnum::NEW->value;
        $payment->save();

        return $payment;
    }

    public static function markAsPaid()
    {

    }
}
