<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Event\Models\Event;
use App\Domain\Identity\Models\User;
use App\Domain\Todo\Models\Todo;
use App\Support\Http\JodiRequest;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
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

        Relation::morphMap([
            'user' => User::class,
            'todo' => Todo::class,
            'event' => Event::class,
        ]);
    }
}
