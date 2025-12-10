<?php

declare(strict_types=1);

namespace App\Support\Sqids;

use Sqids\Sqids;

class Sqid
{
    public static function encode(int $number): string
    {
        return resolve(Sqids::class)->encode([$number]);
    }

    public static function decode(string $sqid): int
    {
        return resolve(Sqids::class)->decode($sqid)[0];
    }
}
