<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Data\Input\UpdateUserData;
use App\Domain\Identity\Models\User;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;
use Spatie\LaravelData\Optional;

class UpdateUser extends JodiAction
{
    public function handle(User $user, UpdateUserData $data): void
    {
        if ($data->preferences instanceof Optional) {
            $preferenceOverrides = [];
        } else {
            $preferenceOverrides = $data->preferences->toArray();
        }

        $preferences = $user->preferences->merge($preferenceOverrides);

        $user->update([
            ...$data->toArray(),
            'preferences' => $preferences,
        ]);
    }

    public function asController(JodiRequest $request): RedirectResponse
    {
        $this->handle($this->user(), UpdateUserData::from($request));

        return back();
    }
}
