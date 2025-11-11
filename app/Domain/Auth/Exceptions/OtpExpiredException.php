<?php

declare(strict_types=1);

namespace App\Domain\Auth\Exceptions;

use App\Exceptions\DisplayException;

class OtpExpiredException extends DisplayException
{
    public function __construct()
    {
        parent::__construct(__('The code is expired.'));
    }
}
