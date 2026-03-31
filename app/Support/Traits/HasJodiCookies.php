<?php

declare(strict_types=1);

namespace App\Support\Traits;

use Illuminate\Http\Request;

/**
 * @mixin Request
 */
trait HasJodiCookies
{
    /**
     * @template T of string|null
     *
     * @param  T  $default
     * @return ($default is null ? string|null : string)
     */
    public function timezone($default = null): ?string
    {
        $timezone = $this->cookies->getString(config('constants.cookies.timezone'));

        if (! $timezone || ! in_array($timezone, timezone_identifiers_list())) {
            return $default;
        }

        return $timezone;
    }

    public function locale(): string
    {
        return $this->cookies->getString(config('constants.cookies.locale'));
    }

    public function deviceId(): string
    {
        return $this->cookies->getString(config('constants.cookies.device_id'));
    }
}
