<?php

declare(strict_types=1);

namespace App\Domain\Event\Actions;

use App\Domain\Event\Data\Input\CreateEventData;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;

class CreateEvent extends JodiAction
{
    public function handle(CreateEventData $data): void
    {
        $this->user()->events()->create([
            ...$data->toArray(),
            'notify_status' => 'waiting',
        ]);
    }

    public function asController(JodiRequest $request): RedirectResponse
    {
        $this->handle(CreateEventData::from($request));

        return back();
    }
}
