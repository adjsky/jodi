<?php

declare(strict_types=1);

namespace App\Http\Requests\Event;

use App\Support\FormRequests\ConvertsToSnakeCase;
use App\Support\Rules\RRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    use ConvertsToSnakeCase;

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'color' => 'nullable|hex_color',
            'startsAt' => 'required|date',
            'endsAt' => 'required|date',
            'notifyAt' => 'required|date',
            'rrule' => ['nullable', 'string', new RRule],
        ];
    }
}
