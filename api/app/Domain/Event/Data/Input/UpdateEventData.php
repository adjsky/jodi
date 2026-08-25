<?php

declare(strict_types=1);

namespace App\Domain\Event\Data\Input;

use App\Support\Data\CastAndTransformers\RRuleCastAndTransformer;
use App\Support\Data\JodiData;
use App\Support\Http\JodiRequest;
use RRule\RRule;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MergeValidationRules]
#[MapOutputName(SnakeCaseMapper::class)]
class UpdateEventData extends JodiData
{
    #[Min(1), Max(120)]
    public string $title;

    #[Max(2_000)]
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

    #[DateFormat('Y-m-d')]
    public ?string $occursAt;

    #[In('this', 'all')]
    public string $scope;

    public static function rules(JodiRequest $request): array
    {
        return [
            'occursAt' => [$request->event->rrule ? 'required' : 'nullable'],
        ];
    }
}
