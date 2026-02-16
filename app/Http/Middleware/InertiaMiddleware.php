<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Inertia\Middleware;

class InertiaMiddleware extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return null;
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string,mixed>
     */
    public function share(Request $request): array
    {
        $deviceId = $request->cookies->get('jodi-device-id');

        return [
            ...parent::share($request),
            'version' => config('jodi.version'),
            'auth' => fn () => [
                'user' => $request->user()
                    ?->only(['id', 'name', 'email', 'preferences']),
                'fcm' => $request->user()
                    ?->pushSubscriptions()
                    ->where('device_id', $deviceId)
                    ->first(['fcm_token as token']),
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
                'success' => fn () => $request->session()->get('success'),
            ],
            'config' => [
                'firebase' => Arr::mapWithKeys(
                    config('services.firebase'),
                    fn ($value, $key) => [Str::camel($key) => $value]
                ),
            ],
        ];
    }
}
