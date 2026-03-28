<?php

declare(strict_types=1);

namespace App\Http\Requests\Todo;

use App\Models\Todo;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReorderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('reorder', [Todo::class, $this->todos]) ?? false;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'todos' => 'required|array',
            'todos.*.id' => 'required|integer',
            'todos.*.position' => 'required|integer|min:1',
            'todos.*.category' => 'nullable|string',
            'todos.*.date' => 'required|date_format:Y-m-d',
        ];
    }
}
