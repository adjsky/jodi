<?php

declare(strict_types=1);

namespace App\Domain\Event\Data\Input;

use App\Support\Rules\RRule;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapOutputName(SnakeCaseMapper::class)]
class CreateEventData extends Data
{
    public string $title;

    public ?string $description;

    #[Rule('hex_color')]
    public ?string $color;

    #[Date]
    public string $startsAt;

    #[Date]
    public string $endsAt;

    #[Date]
    public string $notifyAt;

    #[Rule(new RRule)]
    public ?string $rrule;
}
