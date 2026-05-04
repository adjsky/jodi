<?php

declare(strict_types=1);

namespace App\Domain\Todo\Actions;

use App\Domain\Recurrence\Models\RecurrenceException;
use App\Domain\Todo\Data\Input\UpdateTodoData;
use App\Domain\Todo\Data\Internal\SingleOccurrenceUpdateData;
use App\Domain\Todo\Models\Todo;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UpdateTodo extends JodiAction
{
    public function handle(Todo $todo, UpdateTodoData $data, ?string $timezone): void
    {
        DB::transaction(function () use ($todo, $data, $timezone) {
            if ($this->isSingleOccurrenceUpdate($todo, $data)) {
                $this->updateSingleOccurrence($todo, SingleOccurrenceUpdateData::from($data), $timezone);

                return;
            }

            $this->updateTodo($todo, $data);
        });
    }

    private function isSingleOccurrenceUpdate(Todo $todo, UpdateTodoData $data): bool
    {
        return $data->scope == 'this' && ! is_null($todo->rrule);
    }

    private function updateSingleOccurrence(Todo $todo, SingleOccurrenceUpdateData $data, ?string $timezone): void
    {
        $existingException = $todo->findException($data->occursAt);

        $overrides = $todo->computeOccurrenceOverrides(
            $data->occursAt,
            $data->only('title', 'description', 'color', 'categoryId', 'scheduledAt', 'hasTime', 'notifyAt')->toArray(),
            $existingException
        );

        if (isset($overrides['notify_at']) && isset($existingException->overrides['notify_status'])) {
            $existingException->overrides = Arr::except($existingException->overrides, ['notify_status']);
        }

        $this->resetOccurrencePositions($todo, $data, $overrides, $existingException, $timezone);

        $todo->applyException($data->occursAt, $overrides, $existingException);
    }

    private function resetOccurrencePositions(Todo $todo, SingleOccurrenceUpdateData $data, array $overrides, ?RecurrenceException $existingException, ?string $timezone): void
    {
        if (isset($overrides['scheduled_at'])) {
            $isScheduledAtSameDay = Carbon::parse($overrides['scheduled_at'])->isSameDay($existingException->overrides['scheduled_at'] ?? $data->occursAt);
        } else {
            $isScheduledAtSameDay = true;
        }

        if (isset($overrides['category_id']) || ! $isScheduledAtSameDay) {
            $date = Carbon::parse($data->occursAt, $timezone)->toDateString();
            $todo->positions()->where('date', $date)->delete();
        }
    }

    private function updateTodo(Todo $todo, UpdateTodoData $data): void
    {
        $attributes = $data->toArray();

        if ($data->scope == 'all' && ! is_null($todo->rrule) && ! is_null($data->occursAt)) {
            $todo->deleteExceptions();
            $todo->normalizeRecurringDataForUpdate($attributes, $data->occursAt);
        }

        $this->syncNotificationStatus($todo, $data, $attributes);

        $todo->fill($attributes);

        $this->resetPositions($todo);

        $todo->save();
    }

    private function syncNotificationStatus(Todo $todo, UpdateTodoData $data, array &$attributes): void
    {
        if ($todo->notify_at?->toISOString() === Carbon::make($data->notifyAt)?->toISOString()) {
            return;
        }

        if (is_null($data->notifyAt)) {
            $attributes['notify_status'] = null;
        } else {
            $attributes['notify_status'] = 'waiting';
        }
    }

    private function resetPositions(Todo $todo): void
    {
        $isCategoryChanged = $todo->isDirty('category_id');
        $isScheduledAtSameDay = $todo->scheduled_at->isSameDay($todo->getOriginal('scheduled_at'));

        if ($isCategoryChanged || ! $isScheduledAtSameDay) {
            $todo->positions()->delete();
        }
    }

    public function authorize(JodiRequest $request): bool
    {
        return $this->user()->can('update', $request->todo);
    }

    public function asController(JodiRequest $request, Todo $todo): RedirectResponse
    {
        $this->handle($todo, UpdateTodoData::from($request), $request->timezone());

        return back();
    }
}
