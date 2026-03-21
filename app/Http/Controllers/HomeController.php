<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\EventDto;
use App\Data\TodoDto;
use App\Models\Event;
use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function show(Request $request)
    {
        $search = $request->validate(['d' => 'nullable|date_format:Y-m-d']);
        $tz = $request->cookies->getString('jodi-timezone');

        $date = $search['d'] ?? now($tz)->toDateString();

        $startUtc = Carbon::parse($date, $tz)->startOfDay()->utc();
        $endUtc = Carbon::parse($date, $tz)->endOfDay()->utc();

        return inertia('Home', [
            'todos' => TodoDto::collect($this->todosForDay($date, $startUtc, $endUtc)),
            'events' => EventDto::collect($this->eventsForDay($startUtc, $endUtc)),
            'me' => [
                'nInvitations' => $this->user()->invitations->count(),
                'nFriends' => $this->user()->friends->count(),
            ],
            'categories' => Inertia::defer(
                fn () => $this->user()->categories()->pluck('name'),
            ),
        ]);
    }

    private function todosForDay(string $date, Carbon $start, Carbon $end): Collection
    {
        /** @var Collection<int, Todo> */
        $todos = $this->user()->todos()
            ->with([
                'category',
                'position' => fn ($q) => $q->where('date', $date),
            ])
            ->withPossibleOccurrencesBetween($start, $end)
            ->get()
            ->flatMap(fn ($t) => $t->occurrencesBetween($start, $end));

        return $todos
            ->each(fn ($t) => $t->setAttribute(
                'nth',
                $t->position->first()->position ?? PHP_INT_MAX
            ))
            ->sortBy([
                ['category.name', 'asc'],
                ['nth', 'asc'],
                ['created_at', 'asc'],
            ])
            ->values();
    }

    private function eventsForDay(Carbon $start, Carbon $end): Collection
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
