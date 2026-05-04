<?php

declare(strict_types=1);

namespace App\Domain\Event\Actions;

use App\Domain\Event\Models\Event;
use App\Domain\Identity\Models\User;
use App\Support\Actions\JodiAction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ListEvents extends JodiAction
{
    public function handle(User $user, Carbon $start, Carbon $end): Collection
    {
        $events = Event::query()
            ->forUser($user)
            ->withPossibleOccurrencesBetween($start, $end)
            ->get();

        return $events
            ->flatMap(fn ($e) => $e->occurrencesBetween($start, $end))
            ->sortBy('starts_at')
            ->values();
    }
}
