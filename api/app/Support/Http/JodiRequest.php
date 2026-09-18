<?php

declare(strict_types=1);

namespace App\Support\Http;

use Illuminate\Http\Request;

class JodiRequest extends Request
{
    /**
     * @template T of string|null
     *
     * @param  T  $default
     * @return ($default is null ? string|null : string)
     */
    public function timezone($default = null): ?string
    {
        $timezone = $this->headers->get('X-Timezone');

        if (! $timezone || ! in_array($timezone, timezone_identifiers_list())) {
            return $default;
        }

        return $timezone;
    }

    public function deviceId(): string
    {
        return $this->attributes->get('jodi.device_id');
    }

    public function clientIp(): string
    {
        return $this->attributes->get('jodi.client_ip');
    }
}
