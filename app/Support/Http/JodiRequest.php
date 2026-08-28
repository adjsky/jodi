<?php

declare(strict_types=1);

namespace App\Support\Http;

use App\Support\Concerns\HasJodiCookies;
use Illuminate\Http\Request;

class JodiRequest extends Request
{
    use HasJodiCookies;

    public function ipOrFail(): string
    {
        return $this->ip() ?? throw new \LogicException(__('Unable to resolve client IP.'));
    }
}
