<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\EventDto;
use App\Data\TodoDto;
use App\Models\Category;
use App\Models\Event;
use App\Models\Position;
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

        $startUtc = Carbon::parse($date, $tz)->startOfDay()->setTimezone('UTC');
        $endUtc = Carbon::parse($date, $tz)->endOfDay()->setTimezone('UTC');

        return inertia('Home', [
            'todos' => TodoDto::collect($this->todosForDay($startUtc, $endUtc)),
            'events' => EventDto::collect($this->eventsForDay($startUtc, $endUtc)),
            'me' => [
                'nInvitations' => $this->user()->invitations->count(),
                'nFriends' => $this->user()->friends->count(),
            ],
            'categories' => Inertia::defer(
                fn () => $this->user()->categories->pluck('name'),
            ),
        ]);
    }

    private function todosForDay(Carbon $start, Carbon $end): Collection
    {
        /** @var Collection<int, Todo> */
        $todos = $this->user()->todos()
            ->withPossibleOccurrencesBetween($start, $end)
            ->get()
            ->flatMap(fn ($t) => $t->occurrencesBetween($start, $end));

        $categoryIds = $todos->pluck('category_id')->unique()->filter();
        $categories = Category::whereIn('id', $categoryIds)->get()->keyBy('id');

        $todoIds = $todos->pluck('id')->unique();
        $positions = Position::where('positionable_type', Todo::class)
            ->whereIn('positionable_id', $todoIds)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy('positionable_id');

        return $todos
            ->each(function ($t) use ($categories, $positions) {
                $t->setRelation('category', $categories->get($t->category_id));
                $t->setAttribute('position', $positions->get($t->id)->position ?? PHP_INT_MAX);
            })
            ->sortBy([
                ['category.name', 'asc'],
                ['position', 'asc'],
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
