<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Input;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class RegisterUserData extends Data
{
    #[Min(1), Max((36))]
    public string $name;
}
