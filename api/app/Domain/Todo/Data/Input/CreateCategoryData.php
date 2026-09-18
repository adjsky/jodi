<?php

declare(strict_types=1);

namespace App\Domain\Todo\Data\Input;

use App\Support\Data\JodiData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Unique;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;

#[MergeValidationRules]
class CreateCategoryData extends JodiData
{
    #[Min(1), Max(50)]
    public string $name;

    public static function rules(): array
    {
        return [
            'name' => [new Unique('categories', 'name')->where('user_id', Auth::id())],
        ];
    }
}
