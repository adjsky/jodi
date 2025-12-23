<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Event;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class DaySummaryDto extends Data
{
    public function __construct(
        /** @var DaySummaryEventDto[] */
        public array $events,
        public int $nMore
    ) {}
}

#[TypeScript]
class DaySummaryEventDto extends Data
{
    public function __construct(

        public string $title,
        public ?string $color
    ) {}

    public static function fromModel(Event $event): self
    {
        return new self(
            $event->title,
            $event->color,
        );
    }
}
