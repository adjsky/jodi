<?php

declare(strict_types=1);

use RRule\RRule;

function rrules_match(string|RRule $rrule1, string|RRule $rrule2): bool
{
    $rrule1 = gettype($rrule1) === 'string' ? new RRule($rrule1) : $rrule1;
    $rrule2 = gettype($rrule2) === 'string' ? new RRule($rrule2) : $rrule2;

    return $rrule1->rfcString() === $rrule2->rfcString();
}
