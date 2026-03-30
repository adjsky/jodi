<?php

declare(strict_types=1);

namespace App\Support\Http;

use App\Support\Traits\HasJodiCookies;
use Illuminate\Http\Request;

class JodiRequest extends Request
{
    use HasJodiCookies;
}
