<?php

declare(strict_types=1);

namespace App\Http\Requests\Todo;

use App\Support\FormRequest\ConvertsToSnakeCase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRequest extends FormRequest
{
    use ConvertsToSnakeCase;

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'color' => 'nullable|hex_color',
            'category' => [
                'nullable',
                'string',
                Rule::exists('categories', 'name')->where('user_id', $this->user()?->id),
            ],
            'scheduledAt' => 'required|date',
            'hasTime' => 'required|boolean',
            'notifyAt' => 'nullable|date',
        ];
    }
}
