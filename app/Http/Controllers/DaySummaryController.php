<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\DaySummaryDto;
use App\Data\DaySummaryEventDto;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class DaySummaryController extends Controller
{
    public function get(Request $request, int $year)
    {
        $months = explode(',', $request->query('m', ''));
        $tz = $request->cookies->getString('jodi-timezone');

        $events = $this->user()->events()
            ->whereYear('starts_at', $year)
            ->where(function ($query) use ($year, $months, $tz) {
                foreach ($months as $month) {
                    $date = CarbonImmutable::createFromDate($year, (int) $month, 1, $tz);
                    $startUtc = $date->startOfMonth()->setTimezone('UTC');
                    $endUtc = $date->endOfMonth()->setTimezone('UTC');

                    $query->orWhereBetween('starts_at', [$startUtc, $endUtc]);
                }
            })
            ->orderBy('starts_at')
            ->get(['title', 'color', 'starts_at']);

        $summary = $events
            ->groupBy(fn ($event) => $event->starts_at->setTimezone($tz)->format('Y-m-d'))
            ->map(fn ($events) => new DaySummaryDto(
                DaySummaryEventDto::collect($events->take(2)->toArray()),
                max(0, $events->count() - 2)
            ));

        return response()->json($summary);
    }
}
