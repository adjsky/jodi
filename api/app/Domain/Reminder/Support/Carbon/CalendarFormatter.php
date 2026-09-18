<?php

declare(strict_types=1);

namespace App\Domain\Reminder\Support\Carbon;

use Carbon\CarbonInterface;

class CalendarFormatter
{
    public static function format(CarbonInterface $datetime): string
    {
        $file = __DIR__."/Lang/{$datetime->locale}.php";
        $trans = require file_exists($file) ? $file : __DIR__.'/Lang/en.php';

        return $datetime->calendar(formats: $trans['calendar']);
    }
}
