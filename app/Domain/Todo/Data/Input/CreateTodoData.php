<?php

declare(strict_types=1);

namespace App\Domain\Todo\Data\Input;

use App\Support\Rules\RRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Exists;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MergeValidationRules]
#[MapOutputName(SnakeCaseMapper::class)]
class CreateTodoData extends Data
{
    public string $title;

    public ?string $description;

    #[Rule('hex_color')]
    public ?string $color;

    public ?int $categoryId;

    #[Date]
    public string $scheduledAt;

    public bool $hasTime;

    #[Date]
    public ?string $notifyAt;

    #[Rule(new RRule)]
    public ?string $rrule;

    public static function rules(): array
    {
        return [
            'categoryId' => [new Exists('categories', 'id')->where('user_id', Auth::id())],
        ];
    }
}
