<?php

declare(strict_types=1);

namespace App\Http\Requests\Event;

use App\Support\FormRequest\ConvertsToSnakeCase;
use Illuminate\Foundation\Http\FormRequest;

class DestroyRequest extends FormRequest
{
    use ConvertsToSnakeCase;

    public function authorize(): bool
    {
        return $this->user()?->can('destroy', $this->event) ?? false;
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'occursAt' => [
                $this->event->rrule ? 'required_if:scope,this' : 'nullable',
                'date',
            ],
            'scope' => 'required|in:this,all',
        ];
    }
}
