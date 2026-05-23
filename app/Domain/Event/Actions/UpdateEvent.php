<?php

declare(strict_types=1);

namespace App\Domain\Event\Actions;

use App\Domain\Event\Data\Input\UpdateEventData;
use App\Domain\Event\Data\Internal\SingleOccurrenceUpdateData;
use App\Domain\Event\Models\Event;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RRule\RRule;

class UpdateEvent extends JodiAction
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
        return $data->scope == 'this' && $event->rrule != null;
    }

    private function updateSingleOccurrence(Event $event, SingleOccurrenceUpdateData $data): void
    {
        $existingException = $event->findException($data->occursAt);

        $overrides = $event->computeOccurrenceOverrides(
            $data->occursAt,
            $data->only(...Arr::map(Event::OVERRIDABLE_ATTRIBUTES, Str::camel(...)))->toArray(),
            $existingException
        );

        if (isset($overrides['notify_at']) && isset($existingException->overrides['notify_status'])) {
            $existingException->overrides = Arr::except($existingException->overrides, ['notify_status']);
        }

        $event->applyException($data->occursAt, $overrides, $existingException);
    }

    private function updateEvent(Event $event, UpdateEventData $data): void
    {
        $attributes = $data->except('occursAt', 'scope')->toArray();

        if ($data->scope == 'all' && $event->rrule != null && $data->occursAt != null) {
            $event->resetExceptions(Event::OVERRIDABLE_ATTRIBUTES);
            $event->normalizeRecurringDataForUpdate($attributes, $data->occursAt);

            $this->splitRecurringEvent($event, $data, $attributes);
        }

        $this->syncNotificationStatus($event, $attributes);

        $event->update($attributes);
    }

    private function splitRecurringEvent(Event $event, UpdateEventData $data, array &$attributes): void
    {
        if ($event->rrule && $data->rrule && rrules_match($event->rrule, $data->rrule)) {
            return;
        }

        $until = Carbon::parse($data->startsAt)->subDay()->endOfDay();

        $attributes['rrule'] = new RRule(
            [
                ...new RRule($event->rrule)->getRule(),
                'COUNT' => null,
                'UNTIL' => $until->toIso8601String(),
            ]
        )->rfcString();

        $newEvent = $event->replicate();
        $newEvent->fill($data->except('occursAt', 'scope')->toArray());
        $newEvent->save();

        $this->transferExceptions($event, $newEvent, $until);
    }

    private function transferExceptions(Event $oldEvent, Event $newEvent, Carbon $date): void
    {
        $query = $oldEvent->recurrenceExceptions()->where('occurs_at', '>=', $date);

        if ($newEvent->rrule == null) {
            $query->delete();
        } else {
            $query->update(['recurrenceable_id' => $newEvent->id]);
        }
    }

    private function syncNotificationStatus(Event $event, array &$attributes): void
    {
        if ($event->notify_at->eq($attributes['notify_at'])) {
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
