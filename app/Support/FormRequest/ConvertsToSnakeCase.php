<?php

declare(strict_types=1);

namespace App\Support\FormRequest;

use Illuminate\Support\Str;

trait ConvertsToSnakeCase
{
    /**
     * Get the validated data from the request and map keys to snake_case.
     *
     * @return array<string, mixed>
     */
    public function validatedInSnakeCase(): array
    {
        $validated = $this->validated();
        $snakeCaseData = [];

        foreach ($validated as $key => $value) {
            $snakeCaseData[Str::snake($key)] = $value;
        }

        return $snakeCaseData;
    }
}
