<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LogoutUser extends JodiAction
{
    public function handle(string $deviceId): void
    {
        $this->user()->pushSubscriptions()
            ->where('device_id', $deviceId)
            ->delete();
    }

    public function asController(JodiRequest $request): RedirectResponse
    {
        $this->handle($request->deviceId());

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Inertia::clearHistory();

        return to_route('login');
    }
}
