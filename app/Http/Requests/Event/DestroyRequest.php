<?php

declare(strict_types=1);

namespace App\Http\Requests\Event;

use App\Support\FormRequests\ConvertsToSnakeCase;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DestroyRequest extends FormRequest
{
    use ConvertsToSnakeCase;

    public function authorize(): bool
    {
        return $this->user()?->can('destroy', $this->event) ?? false;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'occursAt' => [
                $this->event->rrule ? 'required_if:scope,this' : 'nullable',
                'date_format:Y-m-d',
            ],
            'scope' => 'required|in:this,all',
        ];
    }
}
