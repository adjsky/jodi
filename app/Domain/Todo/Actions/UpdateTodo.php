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
use Illuminate\Support\Str;
use RRule\RRule;

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
        return $data->scope == 'this' && $todo->rrule != null;
    }

    private function updateSingleOccurrence(Todo $todo, SingleOccurrenceUpdateData $data, ?string $timezone): void
    {
        $existingException = $todo->findException($data->occursAt);

        $overrides = $todo->computeOccurrenceOverrides(
            $data->occursAt,
            $data->only(...Arr::map(Todo::OVERRIDABLE_ATTRIBUTES, Str::camel(...)))->toArray(),
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
        $attributes = $data->except('occursAt', 'scope')->toArray();
        $occursAt = $data->occursAt;

        if ($data->scope == 'all' && $todo->rrule != null && $occursAt != null) {
            $todo->resetExceptions(Todo::OVERRIDABLE_ATTRIBUTES);
            $todo->normalizeRecurringDataForUpdate($attributes, $occursAt);

            $this->splitRecurringTodo($todo, $data, $attributes);
        }

        $this->syncNotificationStatus($todo, $attributes);
        $this->resetPositions($todo, $data);

        $todo->update($attributes);
    }

    private function splitRecurringTodo(Todo $todo, UpdateTodoData $data, array &$attributes): void
    {
        if (! $data->occursAt || ! $data->rrule || ! $todo->rrule || rrules_match($todo->rrule, $data->rrule)) {
            return;
        }

        $until = Carbon::parse($data->scheduledAt)->subDay()->endOfDay();

        $attributes['rrule'] = new RRule(
            [
                ...new RRule($todo->rrule)->getRule(),
                'UNTIL' => $until->toIso8601String(),
            ]
        )->rfcString();

        $newTodo = $todo->replicate();
        $newTodo->fill($data->except('occursAt', 'scope')->toArray());
        $newTodo->save();

        $this->transferExceptions($todo, $newTodo, $until);
        $this->transferPositions($todo, $newTodo, $until);

        $this->resetPositions($newTodo, $data);
    }

    private function transferPositions(Todo $oldTodo, Todo $newTodo, Carbon $date): void
    {
        $oldTodo->positions()
            ->where('date', '>=', $date)
            ->update(['todo_id' => $newTodo->id]);
    }

    private function transferExceptions(Todo $oldTodo, Todo $newTodo, Carbon $date): void
    {
        $query = $oldTodo->recurrenceExceptions()->where('occurs_at', '>=', $date);

        if ($newTodo->rrule == null) {
            $query->delete();
        } else {
            $query->update(['recurrenceable_id' => $newTodo->id]);
        }
    }

    private function syncNotificationStatus(Todo $todo, array &$attributes): void
    {
        if ($todo->notify_at?->toISOString() === Carbon::make($attributes['notify_at'])?->toISOString()) {
            return;
        }

        if ($attributes['notify_at']) {
            $attributes['notify_status'] = 'waiting';
        } else {
            $attributes['notify_status'] = null;
        }
    }

    private function resetPositions(Todo $todo, UpdateTodoData $data): void
    {
        $isCategoryChanged = $todo->category_id != $data->categoryId;

        if ($data->occursAt != null) {
            $isRescheduled = ! Carbon::parse($data->scheduledAt)->isSameDay($data->occursAt);
        } else {
            $isRescheduled = ! $todo->scheduled_at->isSameDay($data->scheduledAt);
        }

        if ($isCategoryChanged || $isRescheduled) {
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
