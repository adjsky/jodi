<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TwoFactorChallengeController extends Controller
{
    public function index(Request $request)
    {
        $email = $request->session()->get('2fa.email');

        if ($email == null) {
            return to_route('login')->with(['message' => 'log in first']);
        }

        return inertia('TwoFactorChallenge', [
            'email' => $email,
        ]);
    }
}
