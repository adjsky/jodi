<?php

declare(strict_types=1);

namespace App\Domain\Event\Actions;

use App\Domain\Event\Data\Input\DestroyEventData;
use App\Domain\Event\Models\Event;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class DestroyEvent extends JodiAction
{
    public function handle(Event $event, DestroyEventData $data): void
    {
        DB::transaction(function () use ($event, $data) {
            if ($data->scope == 'this' && ! is_null($event->rrule)) {
                if (is_null($data->occursAt)) {
                    throw new \LogicException('$data->occursAt must be non-nullable.');
                }
                $event->cancelOccurrence($data->occursAt);
            } else {
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
