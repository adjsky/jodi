<?php

declare(strict_types=1);

namespace App\Infrastructure\Firebase\Exceptions;

use App\Support\Exceptions\DisplayException;
use Illuminate\Http\Response;

class InvalidFirebaseConfiguration extends DisplayException
{
    public function __construct(string $reason)
    {
        parent::__construct(
            "Firebase is misconfigured: {$reason}",
            Response::HTTP_SERVICE_UNAVAILABLE
        );
    }
}
