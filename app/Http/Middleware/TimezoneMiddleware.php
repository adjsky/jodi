<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Http\JodiRequest;
use Symfony\Component\HttpFoundation\Response;

class TimezoneMiddleware
{
    public function handle(JodiRequest $request, \Closure $next): Response
    {
        $user = $request->user();
        $tz = $request->timezone();

        if (is_null($user) || is_null($tz)) {
            return $next($request);
        }

        if ($user->preferences->timezone !== $tz) {
            $user->preferences = $user->preferences->merge(['timezone' => $tz]);
            $user->save();
        }

        return $next($request);
    }
}
