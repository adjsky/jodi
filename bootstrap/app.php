<?php

declare(strict_types=1);

use App\Http\Middleware\InertiaMiddleware;
use App\Http\Middleware\LocaleMiddleware;
use App\Http\Middleware\RequestIdMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            LocaleMiddleware::class,
            RequestIdMiddleware::class,
            InertiaMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: ['push-subscriptions']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(
            function (
                SymfonyResponse $response,
                Throwable $exception,
                Request $request
            ) {
                if ($response->isRedirect()) {
                    return $response;
                }

                $status = $response->getStatusCode();

                if ($status == 419) {
                    return back()->with([
                        'error' => __('The page expired. Please, try again.'),
                    ]);
                }

                $isInertia = $request->header('X-Inertia') == 'true';

                if (! $isInertia) {
                    if (in_array($status, [500, 503, 404])) {
                        return inertia('Error', ['status' => $status])
                            ->toResponse($request)
                            ->setStatusCode($status);
                    }

                    return $response;
                }

                return response()->json(
                    [
                        'exception' => get_class($exception),
                        'message' => $exception->getMessage(),
                    ],
                    $status,
                    collect(['retry-after', 'x-ratelimit-reset'])
                        ->mapWithKeys(
                            fn ($h) => [$h => $response->headers->get($h)]
                        )->all()
                );
            }
        );
    })->create();
