<?php

declare(strict_types=1);

namespace App\Support\Http;

use App\Support\Concerns\HasJodiCookies;
use Illuminate\Foundation\Http\FormRequest;

class JodiFormRequest extends FormRequest
{
    use HasJodiCookies;
}
