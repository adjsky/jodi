<?php

declare(strict_types=1);

namespace App\Support\Data\Contracts;

interface PropertyPreprocessor
{
    public function process(mixed $value): mixed;
}
