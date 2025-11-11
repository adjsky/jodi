<?php

declare(strict_types=1);

namespace App\Domain\Auth\Exceptions;

use App\Exceptions\DisplayException;

class NoUserException extends DisplayException
{
    public function __construct()
    {
        parent::__construct(__('User does not exist.'));
    }
}
