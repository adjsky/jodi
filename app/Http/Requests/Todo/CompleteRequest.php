<?php

declare(strict_types=1);

namespace App\Http\Requests\Todo;

use App\Support\FormRequest\ConvertsToSnakeCase;
use Illuminate\Foundation\Http\FormRequest;

class CompleteRequest extends FormRequest
{
    use ConvertsToSnakeCase;

    public function authorize(): bool
    {
        return $this->user()?->can('complete', $this->todo) ?? false;
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'occursAt' => [
                $this->todo->rrule ? 'required' : 'nullable',
                'date',
            ],
        ];
    }
}
