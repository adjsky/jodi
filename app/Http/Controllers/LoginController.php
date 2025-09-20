<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserOneTimePasswords;
use App\Notifications;
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
            $password = Random::generate(
                UserOneTimePasswords::$SIZE,
                UserOneTimePasswords::$CHARSET
            );

            $user->oneTimePasswords()->create([
                'purpose' => 'login',
                'password' => $password,
                'expires_at' => now()->addMinutes(
                    UserOneTimePasswords::$EXPIRES_IN_X_MINUTES
                ),
            ]);

            $user->notify(new Notifications\OneTimeLoginCode($password));
        }

        $request->session()->put('2fa.email', $email);

        return to_route('two-factor-challenge');
    }
}
