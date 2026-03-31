<?php

declare(strict_types=1);

namespace App\Http\Requests\Event;

use App\Support\Http\JodiFormRequest;
use App\Support\Rules\RRule;
use App\Support\Traits\ConvertsToSnakeCase;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateRequest extends JodiFormRequest
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
            'rrule' => ['nullable', 'string', new RRule],
            'occursAt' => [
                $this->event->rrule ? 'required' : 'nullable',
                'date_format:Y-m-d',
            ],
            'scope' => 'nullable|in:this,all',
        ];
    }
}
