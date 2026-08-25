<?php

declare(strict_types=1);

use App\Http\Middleware\LocaleMiddleware;
use App\Http\Middleware\RequestContextMiddleware;
use App\Http\Middleware\RequestIdMiddleware;
use App\Infrastructure\Firebase\Actions\GetFirebaseMessagingServiceWorker;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        then: function () {
            Route::get(
                '/firebase-messaging-sw.js',
                GetFirebaseMessagingServiceWorker::class
            );
        }
    )
    ->withEvents(discover: [
        __DIR__.'/../app/Domain/*/Listeners',
        __DIR__.'/../app/Infrastructure/*/Listeners',
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
