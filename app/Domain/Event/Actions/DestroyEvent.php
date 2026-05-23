<?php

declare(strict_types=1);

namespace App\Domain\Event\Actions;

use App\Domain\Event\Data\Input\DestroyEventData;
use App\Domain\Event\Models\Event;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use RRule\RRule;

class DestroyEvent extends JodiAction
{
    public function handle(Event $event, DestroyEventData $data): void
    {
        DB::transaction(function () use ($event, $data) {
            switch (true) {
                case $data->scope == 'following' && $event->rrule != null:
                    $until = Carbon::parse($data->getDateOrFail())->subDay()->endOfDay();

                    $event->update([
                        'rrule' => new RRule(
                            [
                                ...new RRule($event->rrule)->getRule(),
                                'COUNT' => null,
                                'UNTIL' => $until->toIso8601String(),
                            ]
                        )->rfcString(),
                    ]);

                    $event->recurrenceExceptions()
                        ->where('occurs_at', '>=', $data->getDateOrFail())
                        ->delete();

                    $event->cancelOccurrence($data->getOccursAtOrFail());

                    break;

                case $data->scope == 'this' && $event->rrule != null:
                    $event->cancelOccurrence($data->getOccursAtOrFail());

                    break;

                default:
                    $event->deleteExceptions();
                    $event->delete();
            }
        });
    }

    public function authorize(JodiRequest $request): bool
    {
        return $this->user()->can('destroy', $request->event);
    }

    public function asController(JodiRequest $request, Event $event): RedirectResponse
    {
        $this->handle($event, DestroyEventData::from($request));

        return back();
    }
}
