<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\OneTimePassword\Purpose;
use App\Exceptions\Service\OneTimePassword\InvalidPasswordException;
use App\Exceptions\Service\OneTimePassword\NoUserException;
use App\Exceptions\Service\OneTimePassword\PasswordExpiredException;
use App\Helpers;
use App\Models\User;
use App\Notifications;
use App\Services\OneTimePasswordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TwoFactorChallengeController extends Controller
{
    public function __construct(
        private OneTimePasswordService $otpService
    ) {}

    public function show(Request $request)
    {
        return inertia('Auth/TwoFactorChallenge');
    }

    public function consume(Request $request)
    {
        ['password' => $password] = $request->validate(['password' => 'required|string']);

        $email = $request->session()->get(
            sprintf('%s.email', config('auth.2fa.session_key'))
        );

        if (! $email) {
            return to_route('login')->with(['error' => 'log in first']);
        }

        Helpers\Auth::throttle(
            'consume-otp',
            config('auth.2fa.throttle.attempts'),
            config('auth.2fa.throttle.decay_seconds'),
            $email
        );

        try {
            $user = $this->otpService->consume(
                Purpose::Login,
                $email,
                $password
            );

            Auth::login($user, remember: true);
            $request->session()->regenerate();

            return redirect()->intended();
        } catch (NoUserException|InvalidPasswordException) {
            throw ValidationException::withMessages([
                'password' => 'wrong code',
            ]);
        } catch (PasswordExpiredException) {
            return redirect()->back()->with('error', 'code expired, log in again');
        }
    }

    public function resend(Request $request)
    {
        $email = $request->session()->get(
            sprintf('%s.email', config('auth.2fa.session_key'))
        );

        if (! $email) {
            return to_route('login')->with(['error' => 'log in first']);
        }

        Helpers\Auth::throttle('resend-otp', 1, config('auth.2fa.resend_otp_throttle'), $email);

        $user = User::where(['email' => $email])->first();

        if ($user) {
            $password = $this->otpService->generate(Purpose::Login, $user);
            $user->notify(new Notifications\OneTimeLoginCode($password));
        }

        return redirect()->back()->with('success', 'code resend');
    }
}
