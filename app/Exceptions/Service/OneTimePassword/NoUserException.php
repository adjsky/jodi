<?php

declare(strict_types=1);

namespace App\Exceptions\Service\OneTimePassword;

use App\Exceptions\DisplayException;

class NoUserException extends DisplayException
{
    public function __construct()
    {
        parent::__construct(__('User does not exist.'));
    }
}
