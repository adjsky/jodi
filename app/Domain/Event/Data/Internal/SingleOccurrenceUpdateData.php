<?php

declare(strict_types=1);

namespace App\Domain\Event\Data\Internal;

use App\Domain\Event\Data\Input\UpdateEventData;
use RRule\RRule;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapOutputName(SnakeCaseMapper::class)]
class SingleOccurrenceUpdateData extends Data
{
    public function __construct(
        public string $title,
        public ?string $description,
        public ?string $color,
        public string $startsAt,
        public string $endsAt,
        public string $notifyAt,
        public ?RRule $rrule,
        public string $occursAt,
        public string $scope
    ) {}

    public static function fromUpdateEventData(UpdateEventData $data): self
    {
        throw_unless($data->occursAt, new \LogicException('$data->occursAt must not be null when updating a single occurence.'));

        return new self(
            $data->title,
            $data->description,
            $data->color,
            $data->startsAt,
            $data->endsAt,
            $data->notifyAt,
            $data->rrule,
            $data->occursAt,
            $data->scope
        );
    }
}
