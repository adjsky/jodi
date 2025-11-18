<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    /**
     * Returns the authenticated user.
     *
     * @throws AuthenticationException if there is no authorized user.
     */
    protected function user(): User
    {
        $user = Auth::user();

        if (! $user) {
            throw new AuthenticationException(
                'Unauthenticated.',
            );
        }

        return $user;
    }
}
