<?php

declare(strict_types=1);

namespace App\Domain\Event\Actions;

use App\Domain\Event\Models\Event;
use App\Support\Actions\JodiAction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ListEvents extends JodiAction
{
    public function handle(Carbon $start, Carbon $end): Collection
    {
        /** @var Collection<int, Event> */
        $events = $this->user()->events()
            ->withPossibleOccurrencesBetween($start, $end)
            ->get();

        return $events
            ->flatMap(fn ($e) => $e->occurrencesBetween($start, $end))
            ->sortBy('starts_at')
            ->values();
    }
}
