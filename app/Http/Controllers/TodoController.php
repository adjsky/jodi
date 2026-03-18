<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Todo\CompleteRequest;
use App\Http\Requests\Todo\CreateRequest;
use App\Http\Requests\Todo\DestroyRequest;
use App\Http\Requests\Todo\ReorderRequest;
use App\Http\Requests\Todo\UpdateRequest;
use App\Models\Position;
use App\Models\Todo;
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
            Position::upsert(
                Arr::map($data['todos'], fn ($t) => [
                    'positionable_type' => Todo::class,
                    'positionable_id' => $t['id'],
                    'date' => $t['date'],
                    'position' => $t['position'],
                ]),
                uniqueBy: ['positionable_type', 'positionable_id', 'date'],
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

        DB::transaction(function () use ($todo, $data) {
            $data['category_id'] = $data['category']
                ? $this->user()->categories()->where('name', $data['category'])->firstOrFail(['id'])->id
                : null;

            if (! is_null($todo->rrule) && $data['scope'] == 'this') {
                $existingException = $todo->findException($data['occurs_at']);

                $overrides = $todo->computeOccurenceOverrides(
                    $data['occurs_at'],
                    Arr::only($data, ['title', 'description', 'color', 'category_id', 'scheduled_at', 'has_time', 'notify_at']),
                    $existingException
                );

                if (isset($overrides['notify_at']) && isset($existingException->overrides['notify_status'])) {
                    $existingException->overrides = Arr::except($existingException->overrides, ['notify_status']);
                }

                $todo->applyException($data['occurs_at'], $overrides, $existingException);

                return back();
            }

            if (! is_null($todo->rrule) && $data['scope'] == 'all') {
                $todo->deleteExceptions();
                $data = $todo->normalizeRecurringDataForUpdate($data);
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

            $todo->update($data);
        });

        // DB::transaction(function () use ($data, $todo) {
        //     $todo->fill([
        //         ...$data,
        //         'category_id' => $data['category']
        //             ? $this->user()->categories()
        //                 ->where('name', $data['category'])
        //                 ->firstOrFail(['id'])
        //                 ->id
        //             : null]
        //     );

        //     $isCategoryChanged = $todo->isDirty('category_id');
        //     $isScheduledAtSameDay = $todo->scheduled_at->isSameDay($todo->getOriginal('scheduled_at'));

        //     if ($isCategoryChanged || ! $isScheduledAtSameDay) {
        //         $todo->position = $todo->getHighestOrderNumber() + 1;
        //     }

        //     $todo->save();

        // });

        return back();
    }

    public function destroy(DestroyRequest $request, Todo $todo)
    {
        $data = $request->validatedInSnakeCase();

        if (is_null($todo->rrule) || $data['scope'] == 'all') {
            $todo->deleteExceptions();
            $todo->delete();
        } else {
            $todo->cancelOccurrence($data['occurs_at']);
        }

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
