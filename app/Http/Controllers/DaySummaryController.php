<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\DaySummaryDto;
use App\Data\DaySummaryEventDto;
use App\Models\Event;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DaySummaryController extends Controller
{
    public function get(Request $request, int $year)
    {
        $months = explode(',', $request->query('m', ''));
        $tz = $request->cookies->getString('jodi-timezone');

        $ranges = collect($months)->map(function ($month) use ($year, $tz) {
            $date = CarbonImmutable::createFromDate($year, (int) $month, 1, $tz);

            return [
                'start' => $date->startOfMonth()->setTimezone('UTC'),
                'end' => $date->endOfMonth()->setTimezone('UTC'),
            ];
        });

        $overallStart = $ranges->min('start');
        $overallEnd = $ranges->max('end');

        /** @var Collection<int, Event> */
        $events = $this->user()->events()
            ->withPossibleOccurrencesBetween($overallStart, $overallEnd)
            ->get(['id', 'title', 'color', 'starts_at', 'rrule']);

        $occurences = $ranges->flatMap(fn ($range) => $events->flatMap(
            fn ($e) => $e->occurrencesBetween($range['start'], $range['end'])
        ));

        $summary = $occurences
            ->sortBy('starts_at')
            ->groupBy(fn ($e) => $e->starts_at->setTimezone($tz)->format('Y-m-d'))
            ->map(fn ($events) => new DaySummaryDto(
                DaySummaryEventDto::collect($events->take(2)->toArray()),
                max(0, $events->count() - 2)
            ));

        return response()->json($summary);
    }
}
