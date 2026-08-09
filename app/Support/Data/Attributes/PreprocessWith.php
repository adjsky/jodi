<?php

declare(strict_types=1);

namespace App\Support\Data\Attributes;

use App\Support\Data\Contracts\PropertyPreprocessor;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PreprocessWith
{
    /** @var list<class-string<PropertyPreprocessor>> */
    public array $processors;

    /** @param class-string<PropertyPreprocessor> ...$processors */
    public function __construct(string ...$processors)
    {
        $this->processors = array_values($processors);
    }
}
