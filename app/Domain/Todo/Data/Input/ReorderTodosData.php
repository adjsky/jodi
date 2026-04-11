<?php

declare(strict_types=1);

namespace App\Domain\Todo\Data\Input;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Exists;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class ReorderTodosData extends Data
{
    #[DataCollectionOf(ReorderTodoData::class)]
    public Collection $todos;
}

#[MergeValidationRules]
class ReorderTodoData extends Data
{
    public int $id;

    #[Min(1)]
    public int $position;

    public ?int $categoryId;

    #[DateFormat('Y-m-d')]
    public string $date;

    public static function rules(): array
    {
        return [
            'categoryId' => [new Exists('categories', 'id')->where('user_id', Auth::id())],
        ];
    }
}
