<?php

declare(strict_types=1);

namespace App\Domain\Todo\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Todo\Data\Input\CreateTodoData;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;

class CreateTodo extends JodiAction
{
    public function handle(User $user, CreateTodoData $data): void
    {
        $user->todos()->create([
            ...$data->toArray(),
            'notify_status' => $data->notifyAt ? 'waiting' : null,
        ]);
    }

    public function asController(JodiRequest $request): RedirectResponse
    {
        $this->handle($this->user(), CreateTodoData::from($request));

        return back();
    }
}
