<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class TimezoneMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
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
