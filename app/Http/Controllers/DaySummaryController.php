<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\DaySummaryDto;
use App\Data\DaySummaryEventDto;
use App\Models\Event;
use App\Support\Http\JodiRequest;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class DaySummaryController extends Controller
{
    public function get(JodiRequest $request, int $year)
    {
        $months = explode(',', $request->query('m', ''));
        $timezone = $request->timezone('UTC');

        $ranges = collect($months)->map(function ($month) use ($year, $timezone) {
            $date = CarbonImmutable::createFromDate($year, (int) $month, 1, $timezone);

            return [
                'start' => $date->startOfMonth()->utc(),
                'end' => $date->endOfMonth()->utc(),
            ];
        });

        $overallStart = $ranges->min('start');
        $overallEnd = $ranges->max('end');

        /** @var Collection<int, Event> */
        $events = $this->user()->events()
            ->withPossibleOccurrencesBetween($overallStart, $overallEnd)
            ->get(['id', 'title', 'color', 'starts_at', 'rrule'])
            ->flatMap(
                fn ($e) => $e->occurrencesBetween($overallStart, $overallEnd)
            );

        $summary = $events
            ->sortBy('starts_at')
            ->groupBy(fn ($e) => $e->starts_at->timezone($timezone)->toDateString())
            ->map(fn ($events) => new DaySummaryDto(
                DaySummaryEventDto::collect($events->take(2)->toArray()),
                max(0, $events->count() - 2)
            ));

        return response()->json($summary);
    }
}
