<?php

declare(strict_types=1);

namespace App\Domain\Todo\Data\Input;

use App\Support\Http\JodiRequest;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class CompleteTodoData extends Data
{
    #[DateFormat('Y-m-d')]
    public ?string $occursAt;

    public static function rules(JodiRequest $request): array
    {
        return [
            'occursAt' => [$request->todo->rrule ? 'required' : 'nullable'],
        ];
    }
}
