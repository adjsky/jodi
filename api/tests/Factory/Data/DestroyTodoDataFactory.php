<?php

declare(strict_types=1);

namespace Tests\Factory\Data;

use App\Domain\Todo\Data\Input\DestroyTodoData;

class DestroyTodoDataFactory
{
    public static function make(array $overrides = []): DestroyTodoData
    {
        return DestroyTodoData::from([], $overrides);
    }
}
