<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Models\User;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LogoutUser extends JodiAction
{
    public function handle(User $user, string $deviceId): void
    {
        $user->pushSubscriptions()
            ->where('device_id', $deviceId)
            ->delete();
    }

    public function asController(JodiRequest $request): RedirectResponse
    {
        $this->handle($this->user(), $request->deviceId());

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Inertia::clearHistory();

        return to_route('login');
    }
}
