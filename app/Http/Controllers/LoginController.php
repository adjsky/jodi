<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\OneTimePassword\Purpose;
use App\Models\User;
use App\Notifications;
use App\Services\OneTimePasswordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct(
        private OneTimePasswordService $otpService
    ) {}

    public function show()
    {
        return inertia('Auth/Login');
    }

    public function login(Request $request)
    {
        $user = User::where(
            $request->validate(['email' => 'required|email'])
        )->first();

        if ($user) {
            $password = $this->otpService->generate(Purpose::Login, $user);
            $user->notify(new Notifications\OneTimeLoginCode($password));
        }

        $request->session()->put(
            sprintf('%s.email', config('auth.2fa.session_key')),
            $request->input('email')
        );

        return to_route('two-factor-challenge');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login');
    }
}
