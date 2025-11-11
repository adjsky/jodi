<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Auth\Enums\OtpPurpose;
use App\Domain\Auth\Exceptions\InvalidOtpException;
use App\Domain\Auth\Exceptions\NoUserException;
use App\Domain\Auth\Exceptions\OtpExpiredException;
use App\Domain\Auth\Notifications;
use App\Domain\Auth\Services\OtpService;
use App\Domain\Auth\Services\ThrottleService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TwoFactorChallengeController extends Controller
{
    public function __construct(
        private OtpService $otpService,
        private ThrottleService $throttleService
    ) {}

    public function show(Request $request)
    {
        return inertia('TwoFactorChallenge');
    }

    public function consume(Request $request)
    {
        ['password' => $password] = $request->validate(['password' => 'required|string']);

        $email = $request->session()->get(
            sprintf('%s.email', config('auth.2fa.session_key'))
        );

        if (! $email) {
            return to_route('login')->with(['error' => __('Log in first.')]);
        }

        $this->throttleService->throttle(
            'consume-otp',
            config('auth.2fa.throttle.attempts'),
            config('auth.2fa.throttle.decay_seconds'),
            $email
        );

        try {
            $user = $this->otpService->consume(
                OtpPurpose::Login,
                $email,
                $password
            );

            Auth::login($user, remember: true);
            $request->session()->regenerate();

            return redirect()->intended();
        } catch (NoUserException|InvalidOtpException) {
            throw ValidationException::withMessages([
                'password' => __('The code is wrong.'),
            ]);
        } catch (OtpExpiredException) {
            return redirect()->back()->with('error', __('The code is expired.'));
        }
    }

    public function resend(Request $request)
    {
        $email = $request->session()->get(
            sprintf('%s.email', config('auth.2fa.session_key'))
        );

        if (! $email) {
            return to_route('login')->with(['error' => __('Log in first.')]);
        }

        $this->throttleService->throttle(
            'resend-otp',
            1,
            config('auth.2fa.resend_otp_throttle'),
            $email
        );

        $user = User::where(['email' => $email])->first();

        if ($user) {
            $password = $this->otpService->generate(OtpPurpose::Login, $user);
            $user->notify(new Notifications\OneTimeLoginCode($password));
        }

        return redirect()->back()->with('success', 'The code has been sent.');
    }
}
