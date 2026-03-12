<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\EventDto;
use App\Data\TodoDto;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
            'todos' => TodoDto::collect(
                $this->user()->todos()
                    ->with('category')
                    ->withPossibleOccurrencesBetween($startUtc, $endUtc)
                    ->get()
                    ->flatMap(fn ($t) => $t->occurrencesBetween($startUtc, $endUtc))
                    ->sortBy([
                        ['category.name', 'asc'],
                        ['position', 'asc'],
                        ['created_at', 'asc'],
                    ])
                    ->values()
            ),
            'events' => EventDto::collect(
                $this->user()->events()
                    ->withPossibleOccurrencesBetween($startUtc, $endUtc)
                    ->get()
                    ->flatMap(fn ($e) => $e->occurrencesBetween($startUtc, $endUtc))
                    ->sortBy('starts_at')
                    ->values()
            ),
            'me' => [
                'nInvitations' => $this->user()->invitations->count(),
                'nFriends' => $this->user()->friends->count(),
            ],
            'categories' => Inertia::defer(
                fn () => $this->user()->categories->pluck('name'),
            ),
        ]);
    }
}
