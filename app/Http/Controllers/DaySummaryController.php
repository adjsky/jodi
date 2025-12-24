<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\DaySummaryDto;
use App\Data\DaySummaryEventDto;
use Illuminate\Http\Request;

class DaySummaryController extends Controller
{
    public function get(Request $request, int $year)
    {
        $months = explode(',', $request->query('m', ''));

        $events = $this->user()->events()
            ->whereYear('starts_at', $year)
            ->where(function ($query) use ($months) {
                foreach ($months as $month) {
                    // TODO: use a date range to account for timezones
                    $query->orWhereMonth('starts_at', $month);
                }
            })
            ->orderBy('starts_at')
            ->get(['title', 'color', 'starts_at']);

        $summary = $events
            ->groupBy(fn ($event) => $event->starts_at->format('Y-m-d'))
            ->map(fn ($events) => new DaySummaryDto(
                DaySummaryEventDto::collect($events->take(2)->toArray()),
                max(0, $events->count() - 2)
            ));

        return response()->json($summary);
    }
}
