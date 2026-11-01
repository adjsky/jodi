<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Data\Input\UpdateUserNameData;
use App\Domain\Identity\Models\User;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\Response;

class UpdateUserName extends JodiAction
{
    public function handle(User $user, UpdateUserNameData $data): void
    {
        $user->update(['name' => $data->name]);
    }

    public function asController(JodiRequest $request): Response
    {
        $this->handle($this->user(), UpdateUserNameData::from($request));

        return response()->noContent();
    }
}
