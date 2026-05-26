<?php

declare(strict_types=1);

namespace App\Domain\Event\Data\Output;

use App\Domain\Event\Models\Event;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class EventData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public ?string $description,
        public ?string $color,
        public ?string $location,
        public ?string $rrule,
        public ?string $recurringSince,
        public ?string $occursAt,
        public CarbonInterface $startsAt,
        public CarbonInterface $endsAt,
        public CarbonInterface $notifyAt,
        public ?CarbonInterface $createdAt,
    ) {}

    public static function fromModel(Event $event): self
    {
        return new self(
            $event->id,
            $event->title,
            $event->description,
            $event->color,
            $event->location,
            $event->rrule,
            $event->recurring_since,
            $event->occurs_at,
            $event->starts_at,
            $event->ends_at,
            $event->notify_at,
            $event->created_at
        );
    }
}
