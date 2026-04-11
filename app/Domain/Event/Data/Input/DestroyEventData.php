<?php

declare(strict_types=1);

namespace App\Domain\Event\Data\Input;

use App\Support\Http\JodiRequest;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class DestroyEventData extends Data
{
    #[DateFormat('Y-m-d')]
    public ?string $occursAt;

    #[In('this', 'all')]
    public string $scope;

    public static function rules(JodiRequest $request): array
    {
        return [
            'occursAt' => [$request->event->rrule ? 'required_if:scope,this' : 'nullable'],
        ];
    }
}
