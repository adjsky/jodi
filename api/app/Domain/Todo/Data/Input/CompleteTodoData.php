<?php

declare(strict_types=1);

namespace App\Domain\Todo\Data\Input;

use App\Support\Data\JodiData;
use App\Support\Http\JodiRequest;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\DateFormat;

#[MergeValidationRules]
class CompleteTodoData extends JodiData
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
