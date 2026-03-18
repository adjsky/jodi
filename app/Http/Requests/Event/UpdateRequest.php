<?php

declare(strict_types=1);

namespace App\Http\Requests\Event;

use App\Rules\ValidRRule;
use App\Support\FormRequest\ConvertsToSnakeCase;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    use ConvertsToSnakeCase;

    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->event) ?? false;
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
            'rrule' => ['nullable', 'string', new ValidRRule],
            'occursAt' => [
                $this->event->rrule ? 'required' : 'nullable',
                'date:Y-m-d',
            ],
            'scope' => 'nullable|in:this,all',
        ];
    }
}
