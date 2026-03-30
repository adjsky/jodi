<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\Http\JodiRequest;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->alias(LaravelRequest::class, JodiRequest::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->environment('production'));
        EncryptCookies::except([
            config('constants.cookies.locale'),
            config('constants.cookies.timezone'),
            config('constants.cookies.device_id'),
        ]);
    }
}
