<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Http\JodiRequest;
use Illuminate\Validation\ValidationException;
use LogicException;
use Symfony\Component\HttpFoundation\Response;

class RequestContextMiddleware
{
    public function handle(JodiRequest $request, \Closure $next): Response
    {
        $deviceId = $request->headers->get('Device-Id');

        if (! $deviceId) {
            throw ValidationException::withMessages([
                'Device-Id' => __('A valid device ID is required.'),
            ]);
        }

        $ip = $request->ip() ?? throw new LogicException(__(
            'Unable to resolve client IP.',
        ));

        $request->attributes->set('jodi.device_id', $deviceId);
        $request->attributes->set('jodi.client_ip', $ip);

        return $next($request);
    }
}
