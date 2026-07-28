<?php

declare(strict_types=1);

namespace App\Domain\Event\Actions;

use App\Domain\Event\Data\Output\CalendarEventData;
use App\Domain\Event\Models\Event;
use App\Domain\Identity\Enums\WeekStart;
use App\Domain\Identity\Models\User;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class ListCalendarEvents extends JodiAction
{
    /**
     * @return Collection<int, CalendarEventData>
     */
    public function handle(User $user, int $year, array $months, string $timezone): Collection
    {
        [$weekStartsAt, $weekEndsAt] = match ($user->preferences->weekStart) {
            WeekStart::Sunday => [CarbonInterface::SUNDAY, CarbonInterface::SATURDAY],
            WeekStart::Monday => [CarbonInterface::MONDAY, CarbonInterface::SUNDAY],
        };

        $ranges = collect($months)->map(function ($month) use ($weekEndsAt, $year, $timezone, $weekStartsAt) {
            $date = CarbonImmutable::createFromDate($year, (int) $month, 1, $timezone);

            return [
                'start' => $date->startOfMonth()->startOfWeek($weekStartsAt)->utc(),
                'end' => $date->endOfMonth()->endOfWeek($weekEndsAt)->utc(),
            ];
        });

        $overallStart = $ranges->min('start');
        $overallEnd = $ranges->max('end');

        $events = Event::query()
            ->forUser($user)
            ->withPossibleOccurrencesBetween($overallStart, $overallEnd)
            ->get(['id', 'title', 'color', 'rrule', 'starts_at', 'ends_at'])
            ->flatMap(
                fn ($e) => $e->occurrencesBetween($overallStart, $overallEnd)
            )
            ->sortBy('starts_at')
            ->values();

        return CalendarEventData::collect($events);
    }

    public function asController(JodiRequest $request, int $year): JsonResponse
    {
        $months = explode(',', $request->query('m', ''));
        $timezone = $request->timezone('UTC');

        $summary = $this->handle($this->user(), $year, $months, $timezone);

        return response()->json($summary);
    }
}
