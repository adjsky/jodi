<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class TwoFactorChallengeController extends Controller
{
    public function __invoke()
    {
        return inertia('TwoFactorChallenge');
    }
}
