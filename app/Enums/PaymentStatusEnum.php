<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentStatusEnum: string
{
    case OPENED = 'Opened';

    case CLOSED = 'Closed';
}
