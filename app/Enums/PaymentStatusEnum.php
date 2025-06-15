<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Перечисления статусов WATA
 *
 * @class PaymentStatusEnum
 */
enum PaymentStatusEnum: string
{
    case OPENED = 'Opened';

    case CLOSED = 'Closed';

    case PAID = 'Paid';

    case DECLINED = 'Declined';

    /**
     * Получить объект перечисления из значения
     *
     * @param string $value
     * @return self
     * @throws \Exception
     */
    public static function getEnumObject(string $value): self
    {
        return self::tryFrom($value) ?? throw new \Exception("Enum $value not found");
    }
}
