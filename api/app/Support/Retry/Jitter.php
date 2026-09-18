<?php

declare(strict_types=1);

namespace App\Support\Retry;

class Jitter
{
    public static function equal(int $attempt, int $base = 30, ?int $cap = null): int
    {
        $max = $base * (2 ** $attempt);

        if ($cap != null) {
            $max = min($max, $cap);
        }

        return (int) ($max / 2) + random_int(0, (int) ($max / 2));
    }

    public static function full(int $attempt, int $base = 30, ?int $cap = null): int
    {
        $max = $base * (2 ** $attempt);

        if ($cap != null) {
            $max = min($max, $cap);
        }

        return random_int(0, $max);
    }
}
