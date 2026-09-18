<?php

declare(strict_types=1);

namespace App\Support\Data\CastAndTransformers;

use Illuminate\Validation\ValidationException;
use RRule\RRule;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Transformers\Transformer;

class RRuleCastAndTransformer implements Cast, Transformer
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): RRule
    {
        try {
            return new RRule($value);
        } catch (\InvalidArgumentException) {
            throw ValidationException::withMessages([
                $property->name => 'Invalid recurrence rule.',
            ]);
        }
    }

    public function transform(DataProperty $property, mixed $value, TransformationContext $context): string
    {
        return $value->rfcString();
    }
}
