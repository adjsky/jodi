<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\Http\JodiRequest;

class SignupController extends Controller
{
    public function __invoke(JodiRequest $request, string $code)
    {
        if (! $request->hasValidSignature()) {
            $request->setFlash('error', __('URL signature is invalid. Request a new invitation.'));

            return to_route('login');
        }

        return inertia('Signup', ['code' => $code]);
    }
}
