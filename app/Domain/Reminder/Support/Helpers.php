<?php

declare(strict_types=1);

namespace App\Domain\Reminder\Support;

use Carbon\CarbonInterface;

class Helpers
{
    public static function startsIn(CarbonInterface $start): string
    {
        $diff = $start->diffInMinutes();

        return $start->fromNow(parts: $diff < 60 ? 1 : 2);
    }
}
