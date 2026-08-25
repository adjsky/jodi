<?php

declare(strict_types=1);

namespace App\Domain\Identity\Enums;

enum WeekStart: string
{
    case Sunday = 'sunday';

    case Monday = 'monday';
}
