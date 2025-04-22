<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\WgStatusEnum;
use App\Models\TelegraphChat;
use App\Models\VpnClient;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;

/**
 * Сервис WireGuard
 *
 * @class WireGuardService
 */
class WireGuardService
{
    /** @var string Интерфейс WireGuard */
    protected string $interface = 'wg0';

    /** @var string Префикс IP-адреса */
    protected string $ipPrefix = '10.66.66.';

    /**
     * Создать конфиг клиента
     *
     * @param TelegraphChat $telegraphChat
     * @return VpnClient
     */
    public function createClient(TelegraphChat $telegraphChat): VpnClient
    {
        $privateKey = trim(shell_exec('wg genkey'));
        $publicKey = trim(shell_exec("echo $privateKey | wg pubkey"));
        $ipAddress = $this->generateUniqueIp();

        $config = $this->buildConfig($privateKey, $ipAddress);
        $configPath = "wireguard/clients/{$telegraphChat->id}_$publicKey.conf";
        $qrPath = "wireguard/qrcodes/{$telegraphChat->id}_$publicKey.png";

        Storage::put($configPath, $config);

        $qr = Builder::create()
            ->data($config)
            ->size(300)
            ->build();
        Storage::put($qrPath, $qr->getString());

        shell_exec("sudo wg set $this->interface peer $publicKey allowed-ips $ipAddress");

        return VpnClient::firstOrCreate([
            'telegraph_chat_id' => $telegraphChat->id,
            'private_key' => $privateKey,
            'public_key' => $publicKey,
            'ip_address' => $ipAddress,
            'status' => WgStatusEnum::ACTIVE->value,
            'config_path' => $configPath,
            'qr_path' => $qrPath,
        ]);
    }

    /**
     * Отключить конфиг клиента
     *
     * @param VpnClient $client
     * @return void
     */
    public function disableClient(VpnClient $client): void
    {
        shell_exec("sudo wg set $this->interface peer $client->public_key remove");

        $client->update(['status' => WgStatusEnum::DISABLED->value]);
    }

    /**
     * Включить конфиг клиента
     *
     * @param VpnClient $client
     * @return void
     */
    public function enableClient(VpnClient $client): void
    {
        shell_exec("sudo wg set $this->interface peer $client->public_key allowed-ips $client->ip_address");

        $client->update(['status' => WgStatusEnum::ACTIVE->value]);
    }

    /**
     * Удалить конфиг клиента
     *
     * @param VpnClient $client
     * @return void
     */
    public function deleteClient(VpnClient $client): void
    {
        shell_exec("sudo wg set $this->interface peer $client->public_key remove");
        Storage::delete([$client->config_path, $client->qr_path]);

        $client->delete();
    }

    /**
     * Собираем конфиг
     *
     * @param string $privateKey
     * @param string $ipAddress
     * @return string
     */
    protected function buildConfig(string $privateKey, string $ipAddress): string
    {
        $serverPublicKey = trim(shell_exec("sudo wg show {$this->interface} public-key"));
        $endpoint = config('telegraph.server_ip');

        return <<<EOL
            [Interface]
            PrivateKey = $privateKey
            Address = $ipAddress
            DNS = 1.1.1.1

            [Peer]
            PublicKey = $serverPublicKey
            Endpoint = $endpoint
            AllowedIPs = 0.0.0.0/0
            PersistentKeepalive = 25
            EOL;
    }

    /**
     * Генерация IP-адреса конфига
     *
     * @return string
     */
    protected function generateUniqueIp(): string
    {
        $base = 2;
        do {
            $ip = "{$this->ipPrefix}{$base}/32";
            $base++;
        } while (VpnClient::where('ip_address', $ip)->exists());

        return $ip;
    }
}
