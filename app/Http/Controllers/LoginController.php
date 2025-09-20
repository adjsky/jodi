<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserOneTimePasswords;
use App\Notifications\LoginOTP;
use Illuminate\Http\Request;
use Nette\Utils\Random;

class LoginController extends Controller
{
    public function index()
    {
        return inertia('Login');
    }

    public function store(Request $request)
    {
        ['email' => $email] = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $email)->first();

        if ($user) {
            $otp = Random::generate(
                UserOneTimePasswords::$SIZE,
                UserOneTimePasswords::$CHARSET
            );

            $user->oneTimePasswords()->create([
                'purpose' => 'login',
                'password' => $otp,
                'expires_at' => now()->addMinutes(
                    UserOneTimePasswords::$EXPIRES_IN_X_MINUTES
                ),
            ]);

            $user->notify(new LoginOtp($otp));
        }

        $request->session()->put('email', $email);

        return to_route('verify-login-otp');
    }
}
