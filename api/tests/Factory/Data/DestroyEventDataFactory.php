<?php

declare(strict_types=1);

namespace Tests\Factory\Data;

use App\Domain\Event\Data\Input\DestroyEventData;

class DestroyEventDataFactory
{
    public static function make(array $overrides = []): DestroyEventData
    {
        return DestroyEventData::from([], $overrides);
    }
}
