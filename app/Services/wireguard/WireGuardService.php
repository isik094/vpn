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
    public function __construct(
        protected string $scriptPath
    ) {
        $this->scriptPath = '/etc/wireguard/wireguard-install.sh';
    }

    /**
     * Добавить нового клиента
     *
     * @param int $name
     * @return bool
     */
    public function addClient(int $name): bool
    {
        if (!file_exists($this->scriptPath)) {
            Log::error("WireGuard script not found at: $this->scriptPath");

            return false;
        }

        $process = new Process([$this->scriptPath]);
        $process->setInput("1\n{$name}\n\n"); // 1 → добавить клиента, имя, дважды Enter
        $process->setTimeout(120);

        try {
            $process->mustRun();

            return true;
        } catch (ProcessFailedException $e) {
            Log::error('Failed added wireguard config: ' . $e->getMessage());

            return false;
        }
    }
}
