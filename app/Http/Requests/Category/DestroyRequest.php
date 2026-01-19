<?php

declare(strict_types=1);

namespace App\Http\Requests\Category;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

class DestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        $category = Category::where('user_id', $user->id)
            ->where('name', $this->route('name'))
            ->first();

        if (! $category) {
            return false;
        }

        $this->merge(['category' => $category]);

        return $user->can('destroy', $category);
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [];
    }
}
