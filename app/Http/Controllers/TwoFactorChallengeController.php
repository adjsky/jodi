<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\OneTimePassword\Purpose;
use App\Http\Requests\TwoFactorChallengeRequest;
use App\Services\OneTimePasswordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorChallengeController extends Controller
{
    public function __construct(
        private OneTimePasswordService $otpService
    ) {}

    public function index(Request $request)
    {
        return inertia('TwoFactorChallenge');
    }

    public function store(TwoFactorChallengeRequest $request)
    {
        $email = $request->session()->get(
            sprintf('%s.email', config('auth.2fa.session_key'))
        );

        if (! $email) {
            return to_route('login')->with(['message' => 'log in first']);
        }

        $request->throttle($email);

        $user = $this->otpService->consume(
            Purpose::Login,
            $email,
            $request->input('password')
        );

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->intended();
    }
}
