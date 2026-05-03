<?php

declare(strict_types=1);

namespace App\Domain\Todo\Actions;

use App\Domain\Todo\Data\Input\CompleteTodoData;
use App\Domain\Todo\Models\Todo;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class CompleteTodo extends JodiAction
{
    public function handle(Todo $todo, CompleteTodoData $data): void
    {
        DB::transaction(function () use ($todo, $data) {
            if (! is_null($todo->rrule) && ! is_null($data->occursAt)) {
                $existingException = $todo->findException($data->occursAt);

                $overrides = [];

                if (isset($existingException->overrides['completed_at'])) {
                    $overrides['completed_at'] = null;
                } else {
                    $overrides['completed_at'] = now();
                }

                $todo->applyException($data->occursAt, $overrides, $existingException);
            } else {
                $todo->completed_at = $todo->completed_at ? null : now();
                $todo->save();
            }
        });
    }

    public function authorize(JodiRequest $request): bool
    {
        return $this->user()->can('complete', $request->todo);
    }

    public function asController(JodiRequest $request, Todo $todo): RedirectResponse
    {
        $this->handle($todo, CompleteTodoData::from($request));

        return back();
    }
}
