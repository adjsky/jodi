<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Http\JodiRequest;
use Closure;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class TimezoneMiddleware
{
    public function handle(JodiRequest $request, Closure $next): Response
    {
        $user = $request->user();
        $tz = $request->timezone();

        if (is_null($user) || is_null($tz)) {
            return $next($request);
        }

        if (Arr::get($user->preferences, 'timezone') !== $tz) {
            $user->preferences = [...$user->preferences, 'timezone' => $tz];
            $user->save();
        }

        return $next($request);
    }
}
