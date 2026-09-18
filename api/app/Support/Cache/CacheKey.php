<?php

declare(strict_types=1);

namespace App\Support\Cache;

use Illuminate\Support\Arr;

class CacheKey
{
    public static function make(string ...$parts): string
    {
        return Arr::join($parts, '|');
    }
}
