<?php

declare(strict_types=1);

namespace App\Support;

use Carbon\Carbon;

class Reminder
{
    public static function startsIn(Carbon $start): string
    {
        $diff = $start->diffInMinutes();

        return $start->fromNow(parts: $diff < 60 ? 1 : 2);
    }
}
