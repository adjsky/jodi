<?php

declare(strict_types=1);

namespace App\Domain\Todo\Actions;

use App\Domain\Todo\Data\Input\CreateTodoData;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;

class CreateTodo extends JodiAction
{
    public function handle(CreateTodoData $data): void
    {
        $this->user()->todos()->create([
            ...$data->toArray(),
            'notify_status' => $data->notifyAt ? 'waiting' : null,
        ]);
    }

    public function asController(JodiRequest $request): RedirectResponse
    {
        $this->handle(CreateTodoData::from($request));

        return back();
    }
}
