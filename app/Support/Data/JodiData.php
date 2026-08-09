<?php

declare(strict_types=1);

namespace App\Support\Data;

use App\Support\Data\Attributes\PreprocessWith;
use Illuminate\Support\Arr;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\DataConfig;

class JodiData extends Data
{
    public static function prepareForPipeline(array $properties): array
    {
        $dataClass = app(DataConfig::class)->getDataClass(static::class);

        foreach ($dataClass->properties as $property) {
            $attribute = $property->attributes->first(PreprocessWith::class);

            if ($attribute == null) {
                continue;
            }

            $name = $property->inputMappedName ?? $property->name;

            if (! Arr::has($properties, $name)) {
                continue;
            }

            $value = Arr::get($properties, $name);

            foreach ($attribute->processors as $processor) {
                $value = new $processor()->process($value);
            }

            Arr::set($properties, $name, $value);
        }

        return $properties;
    }
}
