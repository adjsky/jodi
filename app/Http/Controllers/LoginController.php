<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\OneTimePassword\Purpose;
use App\Models\User;
use App\Notifications;
use App\Services\OneTimePasswordService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct(
        private OneTimePasswordService $otpService
    ) {}

    public function show()
    {
        return inertia('Auth/Login');
    }

    public function store(Request $request)
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
}
