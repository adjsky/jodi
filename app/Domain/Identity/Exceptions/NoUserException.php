<?php

declare(strict_types=1);

namespace App\Domain\Identity\Exceptions;

use App\Support\Exceptions\DisplayException;

class NoUserException extends DisplayException
{
    public function __construct()
    {
        parent::__construct(__('User does not exist.'));
    }
}
