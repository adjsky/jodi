<?php

declare(strict_types=1);

namespace App\Domain\Event\Actions;

use App\Domain\Event\Data\Input\UpdateEventData;
use App\Domain\Event\Data\Internal\SingleOccurrenceUpdateData;
use App\Domain\Event\Models\Event;
use App\Support\Actions\Action;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UpdateEvent extends Action
{
    public function handle(Event $event, UpdateEventData $data): void
    {
        DB::transaction(function () use ($event, $data) {
            if ($this->isSingleOccurrenceUpdate($event, $data)) {
                $this->updateSingleOccurrence($event, SingleOccurrenceUpdateData::from($data));

                return;
            }

            $this->updateEvent($event, $data);
        });
    }

    private function isSingleOccurrenceUpdate(Event $event, UpdateEventData $data): bool
    {
        return $data->scope == 'this' && ! is_null($event->rrule);
    }

    private function updateSingleOccurrence(Event $event, SingleOccurrenceUpdateData $data): void
    {
        $existingException = $event->findException($data->occursAt);

        $overrides = $event->computeOccurrenceOverrides(
            $data->occursAt,
            $data->only('title', 'description', 'color', 'startsAt', 'endsAt', 'notifyAt')->toArray(),
            $existingException
        );

        if (isset($overrides['notify_at']) && isset($existingException->overrides['notify_status'])) {
            $existingException->overrides = Arr::except($existingException->overrides, ['notify_status']);
        }

        $event->applyException($data->occursAt, $overrides, $existingException);
    }

    private function updateEvent(Event $event, UpdateEventData $data): void
    {
        $attributes = $data->toArray();

        if ($data->scope == 'all' && ! is_null($event->rrule) && ! is_null($data->occursAt)) {
            $event->deleteExceptions();
            $event->normalizeRecurringDataForUpdate($attributes, $data->occursAt);
        }

        $this->syncNotificationStatus($event, $data, $attributes);

        $event->update($attributes);
    }

    private function syncNotificationStatus(Event $event, UpdateEventData $data, array &$attributes): void
    {
        if ($event->notify_at->eq($data->notifyAt)) {
            return;
        }

        $attributes['notify_status'] = 'waiting';
    }

    public function authorize(JodiRequest $request): bool
    {
        return $this->user()->can('update', $request->event);
    }

    public function asController(JodiRequest $request, Event $event): RedirectResponse
    {
        $this->handle($event, UpdateEventData::from($request));

        return back();
    }
}
