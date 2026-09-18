<?php

declare(strict_types=1);

use App\Http\Middleware\LocaleMiddleware;
use App\Http\Middleware\RequestContextMiddleware;
use App\Http\Middleware\RequestIdMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withEvents(discover: [
        __DIR__.'/../app/Domain/*/Listeners',
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();

        $middleware->api(
            prepend: [
                RequestContextMiddleware::class,
            ],
            append: [
                LocaleMiddleware::class,
                RequestIdMiddleware::class,
            ],
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(
            fn ($request) => $request->is('api/*') || $request->expectsJson()
        );
    })
    ->create();

$app->useEnvironmentPath(dirname(__DIR__, 2));

return $app;
