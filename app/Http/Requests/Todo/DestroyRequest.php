<?php

declare(strict_types=1);

namespace App\Http\Requests\Todo;

use App\Support\Http\JodiFormRequest;
use App\Support\Traits\ConvertsToSnakeCase;
use Illuminate\Contracts\Validation\ValidationRule;

class DestroyRequest extends JodiFormRequest
{
    use ConvertsToSnakeCase;

    public function authorize(): bool
    {
        return $this->user()?->can('destroy', $this->todo) ?? false;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'occursAt' => [
                $this->todo->rrule ? 'required_if:scope,this' : 'nullable',
                'date_format:Y-m-d',
            ],
            'date' => 'required_if:scope,this|date_format:Y-m-d',
            'scope' => 'required|in:this,all',
        ];
    }
}
