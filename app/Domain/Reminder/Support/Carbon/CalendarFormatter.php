<?php

declare(strict_types=1);

namespace App\Domain\Reminder\Support\Carbon;

use Carbon\CarbonInterface;

class CalendarFormatter
{
    public static function format(CarbonInterface $datetime): string
    {
        $trans = require __DIR__.sprintf('/Lang/%s.php', $datetime->locale);

        return $datetime->calendar(formats: $trans['calendar']);
    }
}
