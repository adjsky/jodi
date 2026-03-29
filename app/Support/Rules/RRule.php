<?php

declare(strict_types=1);

namespace App\Support\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class RRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            new \RRule\RRule($value);
        } catch (\InvalidArgumentException) {
            $fail("The {$attribute} is not a valid recurrence rule.");
        }
    }
}
