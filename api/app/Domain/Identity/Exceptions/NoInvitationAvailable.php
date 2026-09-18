<?php

declare(strict_types=1);

namespace App\Domain\Identity\Exceptions;

use App\Support\Exceptions\DisplayException;
use Illuminate\Contracts\Debug\ShouldntReport;
use Illuminate\Http\Response;

class NoInvitationAvailable extends DisplayException implements ShouldntReport
{
    public function __construct()
    {
        parent::__construct(
            __('This invitation is invalid, expired, or no longer available. Request a new invitation.'),
            Response::HTTP_GONE
        );
    }
}
