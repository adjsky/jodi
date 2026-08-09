<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Input;

use App\Support\Data\Attributes\PreprocessWith;
use App\Support\Data\JodiData;
use App\Support\Data\Preprocessors\EmailPreprocessor;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;

class AuthenticateUserData extends JodiData
{
    #[PreprocessWith(EmailPreprocessor::class)]
    #[Email, Max(254)]
    public string $email;
}
