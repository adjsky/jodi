<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Todo\CompleteRequest;
use App\Http\Requests\Todo\CreateRequest;
use App\Http\Requests\Todo\DestroyRequest;
use App\Http\Requests\Todo\ReorderRequest;
use App\Http\Requests\Todo\UpdateRequest;
use App\Models\Todo;
use App\Models\TodoPosition;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class TodoController extends Controller
{
    public function create(CreateRequest $request)
    {
        $data = $request->validatedInSnakeCase();

        if ($data['notify_at']) {
            $data['notify_status'] = 'waiting';
        }

        $categoryId = $data['category']
            ? $this->user()->categories()
                ->where('name', $data['category'])
                ->firstOrFail(['id'])
                ->id
            : null;

        $this->user()->todos()->create([...$data, 'category_id' => $categoryId]);

        return back();
    }

    public function reorder(ReorderRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            TodoPosition::upsert(
                Arr::map($data['todos'], fn ($t) => [
                    'todo_id' => $t['id'],
                    'date' => $t['date'],
                    'position' => $t['position'],
                ]),
                uniqueBy: ['todo_id', 'date'],
                update: ['position']
            );

            $categories = $this->user()->categories()->pluck('id', 'name');

            foreach ($data['todos'] as $t) {
                if ($t['category']) {
                    $categoryId = $categories->get($t['category']);
                } else {
                    $categoryId = null;
                }

                $this->user()->todos()
                    ->where('id', $t['id'])
                    ->update(['category_id' => $categoryId]);
            }
        });

        return back();
    }

    public function update(UpdateRequest $request, Todo $todo)
    {
        $data = $request->validatedInSnakeCase();
        $timezone = $request->timezone();

        DB::transaction(function () use ($todo, $data, $timezone) {
            $data['category_id'] = $data['category']
                ? $this->user()->categories()->where('name', $data['category'])->firstOrFail(['id'])->id
                : null;

            if (! is_null($todo->rrule) && $data['scope'] == 'this') {
                $existingException = $todo->findException($data['occurs_at']);

                $overrides = $todo->computeOccurrenceOverrides(
                    $data['occurs_at'],
                    Arr::only($data, ['title', 'description', 'color', 'category_id', 'scheduled_at', 'has_time', 'notify_at']),
                    $existingException
                );

                if (isset($overrides['notify_at']) && isset($existingException->overrides['notify_status'])) {
                    $existingException->overrides = Arr::except($existingException->overrides, ['notify_status']);
                }

                if (isset($overrides['scheduled_at'])) {
                    $isScheduledAtSameDay = Carbon::parse($overrides['scheduled_at'])->isSameDay($existingException->overrides['scheduled_at'] ?? $data['occurs_at']);
                } else {
                    $isScheduledAtSameDay = false;
                }

                if (isset($overrides['category_id']) || ! $isScheduledAtSameDay) {
                    $date = Carbon::parse($data['scheduled_at'], $timezone)->toDateString();
                    $todo->positions()->where('date', $date)->delete();
                }

                $todo->applyException($data['occurs_at'], $overrides, $existingException);

                return back();
            }

            if (! is_null($todo->rrule) && $data['scope'] == 'all') {
                $todo->deleteExceptions();
                $data = $todo->normalizeRecurringDataForUpdate($data, $data['occurs_at']);
            }

            if ($data['notify_at']) {
                if ($todo->rrule) {
                    $todo->recurrenceExceptions()
                        ->whereJsonContainsKey('overrides->notify_status')
                        ->update(['overrides' => DB::raw("json_remove(overrides, '$.notify_status')")]);
                } else {
                    $data['notify_status'] = 'waiting';
                }
            } else {
                $data['notify_status'] = null;
            }

            $todo->fill($data);

            $isCategoryChanged = $todo->isDirty('category_id');
            $isScheduledAtSameDay = $todo->scheduled_at->isSameDay($todo->getOriginal('scheduled_at'));

            if ($isCategoryChanged || ! $isScheduledAtSameDay) {
                $todo->positions()->delete();
            }

            $todo->save();
        });

        return back();
    }

    public function destroy(DestroyRequest $request, Todo $todo)
    {
        $data = $request->validatedInSnakeCase();

        DB::transaction(function () use ($todo, $data) {
            if (is_null($todo->rrule) || $data['scope'] == 'all') {
                $todo->deleteExceptions();
                $todo->delete();
            } else {
                $todo->cancelOccurrence($data['occurs_at']);
                $todo->positions()->where('date', $data['date'])->delete();
            }
        });

        return back();
    }

    public function complete(CompleteRequest $request, Todo $todo)
    {
        $data = $request->validatedInSnakeCase();

        DB::transaction(function () use ($todo, $data) {
            if (! is_null($todo->rrule)) {
                $existingException = $todo->findException($data['occurs_at']);

                $overrides = [];

                if (isset($existingException->overrides['completed_at'])) {
                    $overrides['completed_at'] = null;
                } else {
                    $overrides['completed_at'] = now();
                }

                $todo->applyException($data['occurs_at'], $overrides, $existingException);
            } else {
                $todo->completed_at = $todo->completed_at ? null : now();
                $todo->save();
            }
        });

        return back();
    }
}
