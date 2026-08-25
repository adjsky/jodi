<?php

declare(strict_types=1);

namespace App\Domain\Identity\Support;

use App\Domain\Identity\Models\User;
use App\Support\Http\JodiRequest;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelData\Optional;

class AuthenticationCredentialIssuer
{
    public function issueBearerToken(
        User $user,
        string|Optional $deviceName,
    ): string {
        throw_if($deviceName instanceof Optional);

        return $user->createToken($deviceName)->plainTextToken;
    }

    public function startCookieSession(
        JodiRequest $request,
        User $user,
    ): void {
        Auth::guard('web')->login($user, remember: true);

        $request->session()->regenerate();
    }
}
