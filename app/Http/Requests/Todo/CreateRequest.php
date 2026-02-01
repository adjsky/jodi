<?php

declare(strict_types=1);

namespace App\Http\Requests\Todo;

use App\Support\FormRequest\ConvertsToSnakeCase;
use Illuminate\Foundation\Http\FormRequest;

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
            'category' => 'nullable|string|exists:categories,name',
            'color' => 'nullable|hex_color',
            'scheduledAt' => 'required|date',
            'hasTime' => 'sometimes|boolean',
            'notifyAt' => 'sometimes|date',
        ];
    }
}
