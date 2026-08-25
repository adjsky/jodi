<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Input;

use App\Domain\Identity\Enums\AuthenticationCredential;
use App\Support\Data\Attributes\PreprocessWith;
use App\Support\Data\JodiData;
use App\Support\Data\Preprocessors\EmailPreprocessor;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\RequiredIf;
use Spatie\LaravelData\Optional;

class CreateLoginChallengeData extends JodiData
{
    #[PreprocessWith(EmailPreprocessor::class)]
    #[Email, Max(254)]
    public string $email;

    public AuthenticationCredential $credential;

    #[RequiredIf('credential', 'token')]
    #[Min(1), Max(255)]
    public string|Optional $deviceName;
}
