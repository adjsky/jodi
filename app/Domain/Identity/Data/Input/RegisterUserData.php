<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Input;

use App\Support\Data\JodiData;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;

class RegisterUserData extends JodiData
{
    #[Min(1), Max((36))]
    public string $name;
}
