<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Sqids\Sqids;

class SqidsServiceProvider extends ServiceProvider
{
    const PAD = 7;

    const ALPHABET = 'msd793zjyw5rf8v6qxahpgn1bk0etc4u2';

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Sqids::class, function () {
            return new Sqids(self::ALPHABET, self::PAD);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
