<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Request::macro('deviceId', fn () => $this->cookies->getString(config('constants.cookies.device_id')));
        Request::macro('locale', fn () => $this->cookies->getString(config('constants.cookies.locale')));

        Request::macro('timezone', function () {
            $timezone = $this->cookies->getString(config('constants.cookies.timezone'));

            if (! in_array($timezone, timezone_identifiers_list())) {
                return null;
            }

            return $timezone;
        });
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
