<?php

declare(strict_types=1);

namespace App\Enums;

enum CurrencyStatus: int
{
    case ENABLE = 1;
    case DISABLE = 2;
}
