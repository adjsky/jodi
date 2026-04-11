<?php

declare(strict_types=1);

namespace App\Domain\Todo\Actions;

use App\Domain\Todo\Data\Input\DestroyTodoData;
use App\Domain\Todo\Models\Todo;
use App\Support\Actions\Action;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class DestroyTodo extends Action
{
    public function handle(Todo $todo, DestroyTodoData $data): void
    {
        DB::transaction(function () use ($todo, $data) {
            if ($data->scope == 'this' && ! is_null($todo->rrule)) {
                if (is_null($data->occursAt)) {
                    throw new \LogicException('$data->occursAt must be non-nullable.');
                }
                $todo->cancelOccurrence($data->occursAt);
                $todo->positions()->where('date', $data->date)->delete();
            } else {
                $todo->deleteExceptions();
                $todo->delete();
            }
        });
    }

    public function authorize(JodiRequest $request): bool
    {
        return $this->user()->can('destroy', $request->todo);
    }

    public function asController(JodiRequest $request, Todo $todo): RedirectResponse
    {
        $this->handle($todo, DestroyTodoData::from($request));

        return back();
    }
}
