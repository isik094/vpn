<?php

declare(strict_types=1);

namespace App\Services\wireguard;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Сервис для генерации конфигураций протокола WireGuard
 *
 * @class WireGuardService
 */
class WireGuardService
{
    /**
     * Добавить нового клиента
     *
     * @param int $name
     * @return bool
     */
    public static function addClient(int $name): bool
    {
        $scriptPath = '/etc/wireguard/add-wg-client.exp';

        if (!file_exists($scriptPath)) {
            Log::error('Скрипт не найден.');
            return false;
        }

        $process = new Process([$scriptPath, $name]);
        $process->setTimeout(10);

        try {
            $process->mustRun();
            Log::info('Упешно создан конфиг WireGuard.');

            return true;

        } catch (ProcessFailedException $exception) {
            Log::error("Ошибка при создании клиента: {$exception->getMessage()}");

            return false;
        }
    }

    /**
     * Удалить клиента
     *
     * @param int $name
     * @return bool
     */
    public static function removeClient(int $name): bool
    {
        $scriptPath = '/etc/wireguard/remove-wg-client.exp';

        if (!file_exists($scriptPath)) {
            Log::error('Скрипт не найден.');
            return false;
        }

        $process = new Process([$scriptPath, $name]);
        $process->setTimeout(10);

        try {
            $process->mustRun();
            Log::info('Упешно удален конфиг WireGuard.');

            return true;

        } catch (ProcessFailedException $exception) {
            Log::error("Ошибка при удалении клиента: {$exception->getMessage()}");

            return false;
        }
    }
}
