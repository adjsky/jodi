<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Event\CreateRequest;
use App\Http\Requests\Event\DestroyRequest;
use App\Http\Requests\Event\UpdateRequest;
use App\Models\Event;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function create(CreateRequest $request)
    {
        $data = $request->validatedInSnakeCase();
        $data['notify_status'] = 'waiting';

        $this->user()->events()->create($data);

        return back();
    }

    public function update(UpdateRequest $request, Event $event)
    {
        $data = $request->validatedInSnakeCase();

        DB::transaction(function () use ($event, $data) {
            if (! is_null($event->rrule) && $data['scope'] == 'this') {
                $existingException = $event->findException($data['occurs_at']);

                $overrides = $event->computeOccurenceOverrides(
                    $data['occurs_at'],
                    Arr::only($data, ['title', 'description', 'color', 'starts_at', 'ends_at', 'notify_at']),
                    $existingException
                );

                if (isset($overrides['notify_at']) && isset($existingException->overrides['notify_status'])) {
                    $existingException->overrides = Arr::except($existingException->overrides, ['notify_status']);
                }

                $event->applyException($data['occurs_at'], $overrides, $existingException);

                return back();
            }

            if (! is_null($event->rrule) && $data['scope'] == 'all') {
                $event->deleteExceptions();
                $data = $event->normalizeRecurringDataForUpdate($data);
            }

            if ($event->notify_at->ne($data['notify_at'])) {
                if ($event->rrule) {
                    $event->recurrenceExceptions()
                        ->whereJsonContainsKey('overrides->notify_status')
                        ->update(['overrides' => DB::raw("json_remove(overrides, '$.notify_status')")]);
                } else {
                    $data['notify_status'] = 'waiting';
                }
            }

            $event->update($data);
        });

        return back();
    }

    public function destroy(DestroyRequest $request, Event $event)
    {
        $data = $request->validatedInSnakeCase();

        if (is_null($event->rrule) || $data['scope'] == 'all') {
            $event->deleteExceptions();
            $event->delete();
        } else {
            $event->cancelOccurrence($data['occurs_at']);
        }

        return back();
    }
}
