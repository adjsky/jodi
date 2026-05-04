<?php

declare(strict_types=1);

namespace App\Domain\Todo\Data\Input;

use App\Support\Http\JodiRequest;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\RequiredIf;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class DestroyTodoData extends Data
{
    #[DateFormat('Y-m-d')]
    public ?string $occursAt;

    #[RequiredIf('scope', 'this')]
    #[DateFormat('Y-m-d')]
    public ?string $date;

    #[In('this', 'all')]
    public string $scope;

    public static function rules(JodiRequest $request): array
    {
        return [
            'occursAt' => [$request->todo->rrule ? 'required_if:scope,this' : 'nullable'],
        ];
    }
}
