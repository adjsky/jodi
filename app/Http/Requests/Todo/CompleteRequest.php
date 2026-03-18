<?php

declare(strict_types=1);

namespace App\Http\Requests\Todo;

use App\Support\FormRequest\ConvertsToSnakeCase;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CompleteRequest extends FormRequest
{
    use ConvertsToSnakeCase;

    public function authorize(): bool
    {
        return $this->user()?->can('complete', $this->todo) ?? false;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'occursAt' => [
                $this->todo->rrule ? 'required' : 'nullable',
                'date_format:Y-m-d',
            ],
        ];
    }
}
