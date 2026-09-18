<?php

declare(strict_types=1);

namespace Tests\Factory\Data;

use App\Domain\Todo\Data\Input\UpdateTodoData;
use App\Domain\Todo\Models\Todo;

class UpdateTodoDataFactory
{
    public static function make(array $overrides = []): UpdateTodoData
    {
        $todo = Todo::factory()->make();

        return UpdateTodoData::from([
            'title' => $todo->title,
            'description' => $todo->description,
            'color' => $todo->color,
            'scheduledAt' => $todo->scheduled_at->toISOString(),
            'hasTime' => $todo->has_time,
            'notifyAt' => $todo->notify_at?->toISOString(),
            'scope' => 'this',
        ], $overrides);
    }
}
