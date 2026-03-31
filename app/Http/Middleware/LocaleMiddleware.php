<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Http\JodiRequest;
use Closure;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    public function handle(JodiRequest $request, Closure $next): Response
    {
        $locale = $request->user()->preferences['locale'] ??
                 ($request->locale() ?: $request->getPreferredLanguage());

        if (gettype($locale) == 'string') {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
