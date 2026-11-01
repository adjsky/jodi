<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Data\Input\UpdateUserPreferencesData;
use App\Domain\Identity\Models\User;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\Response;

class UpdateUserPreferences extends JodiAction
{
    public function handle(User $user, UpdateUserPreferencesData $data): void
    {
        $preferences = $user->preferences->merge($data->toArray());

        $user->update(['preferences' => $preferences]);
    }

    public function asController(JodiRequest $request): Response
    {
        $this->handle($this->user(), UpdateUserPreferencesData::from($request));

        return response()->noContent();
    }
}
