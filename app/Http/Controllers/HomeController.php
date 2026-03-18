<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\EventDto;
use App\Data\TodoDto;
use App\Models\Category;
use App\Models\Event;
use App\Models\Todo;
use App\Models\TodoPosition;
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
                fn () => $this->user()->categories->pluck('name'),
            ),
        ]);
    }

    private function todosForDay(string $date, Carbon $start, Carbon $end): Collection
    {
        /** @var Collection<int, Todo> */
        $todos = $this->user()->todos()
            ->withPossibleOccurrencesBetween($start, $end)
            ->get()
            ->flatMap(fn ($t) => $t->occurrencesBetween($start, $end));

        $categoryIds = $todos->pluck('category_id')->unique()->filter();
        $categories = Category::whereIn('id', $categoryIds)->get()->keyBy('id');

        $todoIds = $todos->pluck('id')->unique();
        $positions = TodoPosition::whereIn('todo_id', $todoIds)
            ->where('occurs_at', $date)
            ->get()
            ->keyBy('todo_id');

        return $todos
            ->each(function ($t) use ($categories, $positions) {
                $t->setRelation('category', $categories->get($t->category_id));
                $t->setAttribute('order', $positions->get($t->id)->position ?? PHP_INT_MAX);
            })
            ->sortBy([
                ['category.name', 'asc'],
                ['order', 'asc'],
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
