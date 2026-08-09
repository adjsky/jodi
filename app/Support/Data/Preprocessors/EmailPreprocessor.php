<?php

declare(strict_types=1);

namespace App\Support\Data\Preprocessors;

use App\Support\Data\Contracts\PropertyPreprocessor;

class EmailPreprocessor implements PropertyPreprocessor
{
    public function process(mixed $value): mixed
    {
        return is_string($value)
            ? str($value)->trim()->lower()->toString()
            : $value;
    }
}
