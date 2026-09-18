<?php

declare(strict_types=1);

namespace App\Domain\Identity\Exceptions;

use App\Support\Exceptions\DisplayException;
use Illuminate\Contracts\Debug\ShouldntReport;

class OtpExpiredException extends DisplayException implements ShouldntReport
{
    public function __construct()
    {
        parent::__construct(__('The code is expired.'));
    }
}
