<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\OneTimePassword\ConsumeResult;
use App\Enums\OneTimePassword\Purpose;
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

    // TODO: throttling
    public function store(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        $email = $request->session()->get('2fa.email');

        if (! $email) {
            return to_route('login')->with(['message' => 'log in first']);
        }

        $result = $this->otpService->consume(
            Purpose::Login,
            $email,
            $request->input('password')
        );

        switch ($result) {
            case ConsumeResult::NoUser:
            case ConsumeResult::InvalidPassword:
                return back()->with(['message' => 'invalid code']);

            case ConsumeResult::PasswordExpired:
                return back()->with(['message' => 'code expired']);

            case ConsumeResult::Ok:
                Auth::login($result->user, remember: true);
                $request->session()->regenerate();

                return redirect()->intended();
        }
    }
}
