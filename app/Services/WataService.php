<?php

declare(strict_types=1);

namespace App\Services;

use App\helpers\StrHelper;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use League\Config\Exception\InvalidConfigurationException;
use Nette\Utils\Json;

/**
 * Платежный шлюз
 *
 * @class WataPaymentService
 */
class WataService
{
    /** @var string Валюта по умолчанию */
    private const string DEFAULT_CURRENCY = 'RUB';

    /** @var string Базовый адрес продуктивного окружения */
    private static string $baseUri = 'https://api.wata.pro/api/h2h/';

    /** @var Client HTTP-клиент */
    protected Client $client;

    /** @var string API ключ WATA */
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('wata.api_key');

        if (StrHelper::isEmpty($this->apiKey)) {
            throw new InvalidConfigurationException('Не задан API KEY в конфигурации');
        }

        $this->client = new Client([
            'base_uri' => static::$baseUri,
            'headers'  => [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer $this->apiKey",
            ],
        ]);
    }

    /**
     * Создать платежную ссылку
     *
     * @param float $amount
     * @param string $description
     * @param string $orderId
     * @param string $currency
     * @return mixed
     * @throws GuzzleException
     * @throws \Exception
     */
    public function createLink(
        float $amount,
        string $description,
        string $orderId,
        string $currency = self::DEFAULT_CURRENCY,
    ): array {
        $tgBotUrl = config('telegraph.tg_bot_url');

        $params = [
            'amount' => $amount,
            'currency' => $currency,
            'description' => $description,
            'orderId' => $orderId,
            'successRedirectUrl' => $tgBotUrl,
            'failRedirectUrl' => $tgBotUrl,
            'expirationDateTime' => Carbon::now()->addDays(5)->endOfDay()->toIso8601String(),
        ];

        try {
            $response = $this->client->post('links', [
                'json' => $params,
            ]);

            return Json::decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            \Log::error("Error to create payment", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception("Error to create payment: {$e->getMessage()}.");
        }
    }
}
