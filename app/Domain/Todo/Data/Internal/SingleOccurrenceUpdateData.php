<?php

declare(strict_types=1);

namespace App\Domain\Todo\Data\Internal;

use App\Domain\Todo\Data\Input\UpdateTodoData;
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
        public ?int $categoryId,
        public string $scheduledAt,
        public bool $hasTime,
        public ?string $notifyAt,
        public ?string $rrule,
        public string $occursAt,
        public string $scope
    ) {}

    public static function fromUpdateTodoData(UpdateTodoData $data): self
    {
        if (is_null($data->occursAt)) {
            throw new \LogicException('$data->occursAt must be non-nullable.');
        }

        return new self(
            $data->title,
            $data->description,
            $data->color,
            $data->categoryId,
            $data->scheduledAt,
            $data->hasTime,
            $data->notifyAt,
            $data->rrule,
            $data->occursAt,
            $data->scope
        );
    }
}
