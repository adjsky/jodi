<?php

declare(strict_types=1);

namespace App\Domain\Todo\Data\Input;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Unique;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class CreateCategoryData extends Data
{
    public string $name;

    public static function rules(): array
    {
        return [
            'name' => [new Unique('categories', 'name')->where('user_id', Auth::id())],
        ];
    }
}
