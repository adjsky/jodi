<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\TransientToken;

class LogoutUser extends JodiAction
{
    public function asController(JodiRequest $request): Response
    {
        $user = $this->user();

        $user->pushSubscriptions()
            ->where('device_id', '=', $request->deviceId())
            ->delete();

        $accessToken = $user->currentAccessToken();

        switch (true) {
            case $accessToken instanceof PersonalAccessToken:
                $accessToken->delete();

            case $accessToken instanceof TransientToken:
                Auth::guard('web')->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

        }

        return response()->noContent();
    }
}
