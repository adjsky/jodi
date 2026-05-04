<?php

declare(strict_types=1);

namespace App\Domain\Event\Data\Output;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class DaySummaryData extends Data
{
    public function __construct(
        /** @var DaySummaryEventData[] */
        public array $events,
        public int $nMore
    ) {}
}

#[TypeScript]
class DaySummaryEventData extends Data
{
    public function __construct(
        public string $title,
        public ?string $color
    ) {}
}
