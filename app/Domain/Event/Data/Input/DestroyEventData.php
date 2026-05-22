<?php

declare(strict_types=1);

namespace App\Domain\Event\Data\Input;

use App\Support\Http\JodiRequest;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\RequiredIf;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class DestroyEventData extends Data
{
    #[DateFormat('Y-m-d')]
    public ?string $occursAt;

    #[RequiredIf('scope', 'following')]
    #[DateFormat('Y-m-d')]
    public ?string $date;

    #[In('this', 'following', 'all')]
    public string $scope;

    public static function rules(JodiRequest $request): array
    {
        return [
            'occursAt' => [$request->event->rrule ? 'required_if:scope,this,following' : 'nullable'],
        ];
    }

    public function getOccursAtOrFail(): string
    {
        return $this->occursAt ?? throw new \LogicException(
            '$this->occursAt must not be null when scope is "this" or "following".'
        );
    }

    public function getDateOrFail(): string
    {
        return $this->date ?? throw new \LogicException(
            '$this->date must not be null when scope is "following".'
        );
    }
}
