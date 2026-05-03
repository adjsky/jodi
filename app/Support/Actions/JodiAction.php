<?php

declare(strict_types=1);

namespace App\Support\Actions;

use App\Domain\Identity\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

abstract class JodiAction
{
    use AsAction;

    /**
     * Returns the authenticated user.
     *
     * @throws AuthenticationException if there is no authorized user.
     */
    protected function user(): User
    {
        $user = Auth::user();

        if (! $user) {
            throw new AuthenticationException('Unauthenticated.');
        }

        return $user;
    }
}
