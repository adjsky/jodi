<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\OneTimePassword\Purpose;
use App\Exceptions\Service\OneTimePassword\InvalidPasswordException;
use App\Exceptions\Service\OneTimePassword\NoUserException;
use App\Exceptions\Service\OneTimePassword\PasswordExpiredException;
use App\Http\Requests\TwoFactorChallengeRequest;
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

    public function store(TwoFactorChallengeRequest $request)
    {
        $email = $request->session()->get(
            sprintf('%s.email', config('auth.2fa.session_key'))
        );

        if (! $email) {
            return to_route('login')->with(['error' => 'log in first']);
        }

        $request->throttle($email);

        try {
            $user = $this->otpService->consume(
                Purpose::Login,
                $email,
                $request->input('password')
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
}
