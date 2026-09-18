<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Event\Models\Event;
use App\Domain\Identity\Models\User;
use App\Domain\Todo\Models\Todo;
use App\Support\Http\JodiRequest;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider as LaravelTelescopeServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->alias(LaravelRequest::class, JodiRequest::class);

        if ($this->app->environment('local') && class_exists(LaravelTelescopeServiceProvider::class)) {
            $this->app->register(LaravelTelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->environment('production'));
        Date::use(CarbonImmutable::class);

        Relation::morphMap([
            'user' => User::class,
            'todo' => Todo::class,
            'event' => Event::class,
        ]);
    }
}
