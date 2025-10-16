<?php

declare(strict_types=1);

namespace App\Exceptions\Service\OneTimePassword;

use App\Exceptions\DisplayException;

class InvalidPasswordException extends DisplayException
{
    public function __construct()
    {
        parent::__construct('invalid code');
    }
}
