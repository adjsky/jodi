<?php

declare(strict_types=1);

namespace App\Http\Requests\Category;

use App\Support\Http\JodiFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class CreateRequest extends JodiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
        ];
    }
}
