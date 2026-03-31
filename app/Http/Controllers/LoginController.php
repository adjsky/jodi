<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Auth\Enums\OtpPurpose;
use App\Domain\Auth\Notifications;
use App\Domain\Auth\Services\OtpService;
use App\Models\User;
use App\Support\Http\JodiRequest;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function __construct(
        private OtpService $otpService
    ) {}

    public function show(JodiRequest $request)
    {
        return inertia('Login');
    }

    public function login(JodiRequest $request)
    {
        $user = User::where(
            $request->validate(['email' => 'required|email'])
        )->first();

        if ($user) {
            $password = $this->otpService->generate(OtpPurpose::Login, $user);
            $user->notify(new Notifications\OneTimeLoginCode($password));
        }

        $request->session()->put(
            sprintf('%s.email', config('auth.2fa.session_key')),
            $request->input('email')
        );

        return to_route('two-factor-challenge');
    }

    public function logout(JodiRequest $request)
    {
        $this->user()->pushSubscriptions()
            ->where('device_id', $request->deviceId())
            ->delete();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Inertia::clearHistory();

        return to_route('login');
    }
}
