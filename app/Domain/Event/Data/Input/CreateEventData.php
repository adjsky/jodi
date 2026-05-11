<?php

declare(strict_types=1);

namespace App\Domain\Event\Data\Input;

use App\Support\Data\CastAndTransformers\RRuleCastAndTransformer;
use RRule\RRule;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;
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

    #[WithCastAndTransformer(RRuleCastAndTransformer::class)]
    public ?RRule $rrule;
}
