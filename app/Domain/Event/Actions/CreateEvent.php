<?php

declare(strict_types=1);

namespace App\Domain\Event\Actions;

use App\Domain\Event\Data\Input\CreateEventData;
use App\Domain\Identity\Models\User;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;

class CreateEvent extends JodiAction
{
    public function handle(User $user, CreateEventData $data): void
    {
        $user->events()->create([
            ...$data->toArray(),
            'notify_status' => 'waiting',
        ]);
    }

    public function asController(JodiRequest $request): RedirectResponse
    {
        $this->handle($this->user(), CreateEventData::from($request));

        return back();
    }
}
