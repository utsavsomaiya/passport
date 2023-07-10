<?php

declare(strict_types=1);

namespace App\Enums;

use ArchTech\Enums\Values;

enum CurrencyStatus: int
{
    use Values;

    case ENABLE = 1;
    case DISABLE = 2;
}
