<?php

declare(strict_types=1);

namespace Tests\Factory\Data;

use App\Domain\Event\Data\Input\UpdateEventData;
use App\Domain\Event\Models\Event;

class UpdateEventDataFactory
{
    public static function make(array $overrides = []): UpdateEventData
    {
        $event = Event::factory()->make();

        return UpdateEventData::from([
            'title' => $event->title,
            'description' => $event->description,
            'color' => $event->color,
            'startsAt' => $event->starts_at->toISOString(),
            'endsAt' => $event->ends_at->toISOString(),
            'notifyAt' => $event->notify_at->toISOString(),
            'scope' => 'this',
        ], $overrides);
    }
}
