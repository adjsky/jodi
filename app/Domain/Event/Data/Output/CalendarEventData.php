<?php

declare(strict_types=1);

namespace App\Domain\Event\Data\Output;

use App\Domain\Event\Models\Event;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CalendarEventData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public ?string $color,
        public CarbonInterface $startsAt,
        public CarbonInterface $endsAt,
        public ?string $occursAt
    ) {}

    public static function fromModel(Event $event): self
    {
        return new self(
            $event->id,
            $event->title,
            $event->color,
            $event->starts_at,
            $event->ends_at,
            $event->occurs_at
        );
    }
}
