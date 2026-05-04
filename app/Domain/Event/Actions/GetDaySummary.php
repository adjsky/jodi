<?php

declare(strict_types=1);

namespace App\Domain\Event\Actions;

use App\Domain\Event\Data\Output\DaySummaryData;
use App\Domain\Event\Data\Output\DaySummaryEventData;
use App\Domain\Event\Models\Event;
use App\Domain\Identity\Models\User;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class GetDaySummary extends JodiAction
{
    public function handle(User $user, int $year, array $months, string $timezone): Collection
    {
        $ranges = collect($months)->map(function ($month) use ($year, $timezone) {
            $date = CarbonImmutable::createFromDate($year, (int) $month, 1, $timezone);

            return [
                'start' => $date->startOfMonth()->utc(),
                'end' => $date->endOfMonth()->utc(),
            ];
        });

        $overallStart = $ranges->min('start');
        $overallEnd = $ranges->max('end');

        $events = Event::query()
            ->forUser($user)
            ->withPossibleOccurrencesBetween($overallStart, $overallEnd)
            ->get(['id', 'title', 'color', 'starts_at', 'rrule'])
            ->flatMap(
                fn ($e) => $e->occurrencesBetween($overallStart, $overallEnd)
            );

        $summary = $events
            ->sortBy('starts_at')
            ->groupBy(fn ($e) => $e->starts_at->timezone($timezone)->toDateString())
            ->map(fn ($events) => new DaySummaryData(
                DaySummaryEventData::collect($events->take(2)->toArray()),
                max(0, $events->count() - 2)
            ));

        return $summary;
    }

    public function asController(JodiRequest $request, int $year): JsonResponse
    {
        $months = explode(',', $request->query('m', ''));
        $timezone = $request->timezone('UTC');

        $summary = $this->handle($this->user(), $year, $months, $timezone);

        return response()->json($summary);
    }
}
