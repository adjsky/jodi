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
        $timezone = $request->timezone();

        if (! $user || ! $timezone) {
            return $next($request);
        }

        if ($user->preferences->timezone !== $timezone) {
            $user->preferences = $user->preferences->merge(['timezone' => $timezone]);
            $user->save();
        }

        return $next($request);
    }
}
