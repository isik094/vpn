<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Перечисления статусов конфигов WG
 */
enum WgStatusEnum: string
{
    /** Активный */
    case ACTIVE = 'active';

    /** Отключенный */
    case DISABLED = 'disabled';
}
