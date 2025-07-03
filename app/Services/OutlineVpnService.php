<?php

declare(strict_types=1);

namespace App\Services;

use App\helpers\StrHelper;
use App\Models\VpnKey;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Casts\Json;
use League\Config\Exception\InvalidConfigurationException;

/**
 * Сервис для генерации ключей
 *
 * @class OutlineVpnService
 */
class OutlineVpnService
{
    /** @var Client HTTP-клиент */
    private Client $client;

    /** @var string Outline url */
    private string $apiUrl;

    /** @var string Сертификат */
    private string $apiCertSha256;

    /**
     * Получения данных для подключения
     */
    public function __construct()
    {
        $this->apiUrl = config('outline.api_url');
        $this->apiCertSha256 = config('outline.api_cert_sha256');

        if (StrHelper::isEmpty($this->apiUrl) || StrHelper::isEmpty($this->apiCertSha256)) {
            throw new InvalidConfigurationException('Invalid configuration');
        }

        $this->client = new Client([
            'verify' => false,
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    /**
     * Сгенерировать ключ VPN
     *
     * @param int $chatId
     * @return VpnKey|null
     * @throws GuzzleException
     * @throws \Exception
     */
    public function createKey(int $chatId): ?VpnKey
    {
        try {
            $response = $this->client->request('POST', "$this->apiUrl/access-keys");

            if ($response->getStatusCode() === 201) {
                $data = Json::decode($response->getBody()->getContents());

                if (empty($data)) {
                    throw new \Exception('Возникла ошибка парсинге данных');
                }

                $vpnKey = new VpnKey();
                $vpnKey->chat_id = $chatId;
                $vpnKey->key_id = $data['id'] ?? null;
                $vpnKey->name = $data['name'] ?? null;
                $vpnKey->password = $data['password'] ?? null;
                $vpnKey->port = $data['port'] ?? null;
                $vpnKey->method = $data['method'] ?? null;
                $vpnKey->accessUrl = $data['accessUrl'] ?? null;
                $vpnKey->save();

                return $vpnKey;
            }

            return null;
        } catch (\Exception $e) {
            \Log::error("Возникла ошибка при создании ключа VPN {$e->getMessage()}");

            throw new \Exception('Возникла ошибка при генерации ключа Outline VPN');
        }
    }

    /**
     * Удалить ключ VPN
     *
     * @param int $keyId
     * @return bool
     * @throws GuzzleException
     */
    public function deleteKey(int $keyId): bool
    {
        $response = $this->client->request('DELETE',"$this->apiUrl/access-keys/$keyId");

        if ($response->getStatusCode() === 204) {
            return true;
        }

        return false;
    }

    /**
     * Сгенерировать текст сообщения
     *
     * @param string $key
     * @param int $paymentId
     * @param bool $extend - флаг продления
     * @return string
     */
    public function getMessage(string $key, int $paymentId, bool $extend = false): string
    {
        if ($extend) {
            $text = <<<MARKDOWN
        #️⃣ № заказа *$paymentId*

        🔑 *Ключ доступа успешно продлен*
        MARKDOWN;
        } else {
            $text = <<<MARKDOWN
        #️⃣ № заказа *$paymentId*

        🔑 *Ключ доступа:*
        `$key`

        🫵 нажмите на ключ чтобы скопировать

        ❗*Не передавайте ключ третьим лицам*❗
        MARKDOWN;
        }

        return $text;
    }
}
