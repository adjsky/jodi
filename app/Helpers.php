<?php

declare(strict_types=1);

use DeviceDetector\Cache\LaravelCache;
use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use RRule\RRule;

function rrules_match(string|RRule $rrule1, string|RRule $rrule2): bool
{
    $rrule1 = is_string($rrule1) ? new RRule($rrule1) : $rrule1;
    $rrule2 = is_string($rrule2) ? new RRule($rrule2) : $rrule2;

    return $rrule1->rfcString() === $rrule2->rfcString();
}

function detect_device(?string $userAgent): DeviceDetector
{
    $dd = new DeviceDetector($userAgent ?? '', ClientHints::factory($_SERVER));
    $dd->setCache(new LaravelCache);
    $dd->parse();

    return $dd;
}
