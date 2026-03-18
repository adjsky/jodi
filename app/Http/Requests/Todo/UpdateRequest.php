<?php

declare(strict_types=1);

namespace App\Http\Requests\Todo;

use App\Rules\ValidRRule;
use App\Support\FormRequest\ConvertsToSnakeCase;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    use ConvertsToSnakeCase;

    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->todo) ?? false;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
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
            'rrule' => ['nullable', 'string', new ValidRRule],
            'occursAt' => [
                $this->todo->rrule ? 'required' : 'nullable',
                'date:Y-m-d',
            ],
            'scope' => 'nullable|in:this,all',
        ];
    }
}
