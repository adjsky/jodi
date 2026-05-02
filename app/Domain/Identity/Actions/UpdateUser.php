<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Data\Input\UpdateUserData;
use App\Support\Actions\Action;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;
use Spatie\LaravelData\Optional;

class UpdateUser extends Action
{
    public function handle(UpdateUserData $data): void
    {
        if ($data->preferences instanceof Optional) {
            $preferenceOverrides = [];
        } else {
            $preferenceOverrides = $data->preferences->toArray();
        }

        $preferences = $this->user()->preferences->merge($preferenceOverrides);

        $this->user()->update([
            ...$data->toArray(),
            'preferences' => $preferences,
        ]);
    }

    public function asController(JodiRequest $request): RedirectResponse
    {
        $this->handle(UpdateUserData::from($request));

        return back();
    }
}
