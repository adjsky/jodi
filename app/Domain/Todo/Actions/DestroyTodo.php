<?php

declare(strict_types=1);

namespace App\Domain\Todo\Actions;

use App\Domain\Todo\Data\Input\DestroyTodoData;
use App\Domain\Todo\Models\Todo;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use RRule\RRule;

class DestroyTodo extends JodiAction
{
    public function handle(Todo $todo, DestroyTodoData $data): void
    {
        DB::transaction(function () use ($todo, $data) {
            switch (true) {
                case $data->scope == 'following':
                    $until = Carbon::parse($data->getDateOrFail())->subDay()->endOfDay();

                    $todo->update([
                        'rrule' => new RRule(
                            [
                                ...new RRule($todo->rrule)->getRule(),
                                'UNTIL' => $until->toIso8601String(),
                            ]
                        )->rfcString(),
                    ]);

                    $todo->cancelOccurrence($data->getOccursAtOrFail());
                    $todo->positions()->where('date', '>=', $data->getDateOrFail())->delete();

                    $todo->recurrenceExceptions()
                        ->where('occurs_at', '>', $data->getDateOrFail())
                        ->delete();

                    break;

                case $data->scope == 'this' && $todo->rrule != null:
                    $todo->cancelOccurrence($data->getOccursAtOrFail());
                    $todo->positions()->where('date', $data->getDateOrFail())->delete();

                    break;

                default:
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
