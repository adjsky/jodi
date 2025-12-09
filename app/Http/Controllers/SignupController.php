<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SignupController extends Controller
{
    public function show(Request $request)
    {
        if (! $request->hasValidSignature()) {
            return to_route('login')->with(['error' => __('URL signature is invalid. Request a new invitation.')]);
        }

        return inertia('Signup');
    }

    public function signup(Request $request) {}
}
