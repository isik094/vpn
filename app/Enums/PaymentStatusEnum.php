<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Статус платежа
 */
enum PaymentStatusEnum: int
{
    /** Новый */
    case NEW = 0;

    /** Оплачен */
    case PAID = 1;

    /** Возврат */
    case RETURN = 6;

    /** Ошибка */
    case ERROR = 8;

    /** Отмена */
    case CANCEL = 9;
}
