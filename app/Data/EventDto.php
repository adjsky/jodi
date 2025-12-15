<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Event;
use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class EventDto extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public ?string $description,
        public ?string $location,
        public bool $isAllDay,
        public Carbon $startsAt,
        public ?Carbon $endsAt,
        public ?Carbon $createdAt,
    ) {}

    public static function fromModel(Event $event): self
    {
        return new self(
            $event->id,
            $event->title,
            $event->description,
            $event->location,
            $event->is_all_day,
            $event->starts_at,
            $event->ends_at,
            $event->created_at
        );
    }
}
