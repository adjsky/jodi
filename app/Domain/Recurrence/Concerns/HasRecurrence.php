<?php

declare(strict_types=1);

namespace App\Domain\Recurrence\Concerns;

use App\Domain\Recurrence\Models\RecurrenceException;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use RRule\RRule;

/**
 * @property ?string $occurs_at
 * @property ?string $recurring_since
 */
trait HasRecurrence
{
    public function occurrencesBetween(CarbonInterface $viewStart, CarbonInterface $viewEnd): Collection
    {
        if (is_null($this->rrule)) {
            $model = $this->replicate();
            $model->id = $this->id;

            return collect([$model]);
        }

        $exceptions = $this->recurrenceExceptions->keyBy(
            fn ($e) => $e->occurs_at->toDateString()
        );
        $recurrenceStartKey = $this->recurrenceStartKey();
        $dtstart = $this->getAttribute($recurrenceStartKey);

        $rrule = new RRule($this->rrule, $dtstart);

        $occurrences = collect($rrule->getOccurrencesBetween($viewStart, $viewEnd))
            ->keyBy(fn ($dt) => Carbon::instance($dt)->toDateString())
            ->map(fn ($dt) => Carbon::instance($dt));

        foreach ($exceptions as $datestr => $exception) {
            if ($exception->is_cancelled) {
                $occurrences->forget($datestr);

                continue;
            }

            if (! isset($exception->overrides[$recurrenceStartKey])) {
                continue;
            }

            $start = Carbon::parse($exception->overrides[$recurrenceStartKey]);

            if ($start->between($viewStart, $viewEnd)) {
                $occurrences->put($datestr, Carbon::parse($datestr));
            } else {
                $occurrences->forget($datestr);
            }
        }

        return $occurrences
            ->map(function ($date, $datestr) use ($dtstart, $exceptions) {
                $exception = $exceptions->get($datestr);

                $model = $this->replicate();
                $model->id = $this->id;
                $model->occurs_at = $datestr;
                $model->recurring_since = $dtstart->toDateString();

                foreach ($this->listDateAttributes() as $key) {
                    if (isset($exception->overrides[$key])) {
                        continue;
                    }

                    $attribute = $this->getAttribute($key);

                    if (is_null($attribute)) {
                        continue;
                    }

                    $offset = $dtstart->diffInDays($attribute);
                    $model->setAttribute($key, $date->copy()->addDays($offset)->setTimeFrom($attribute));
                }

                if ($exception) {
                    foreach ($exception->overrides as $key => $value) {
                        $model->setAttribute($key, $value);
                    }
                }

                return $model;
            })
            ->values();
    }

    public function applyException(string $occursAt, array $overrides, ?RecurrenceException $existingException): void
    {
        if (is_null($existingException)) {
            $this->recurrenceExceptions()->create(
                ['occurs_at' => $occursAt, 'is_cancelled' => false, 'overrides' => $overrides]
            );
        } else {
            $this->recurrenceExceptions()
                ->where('occurs_at', $occursAt)
                ->update(['overrides' => [...$existingException->overrides, ...$overrides]]);
        }
    }

    public function findException(string $occursAt): ?RecurrenceException
    {
        return $this->recurrenceExceptions()
            ->where('occurs_at', $occursAt)
            ->sharedLock()
            ->first();
    }

    public function deleteExceptions(?string $occursAt = null): void
    {
        $qb = $this->recurrenceExceptions();

        if (! is_null($occursAt)) {
            $qb = $qb->where('occurs_at', $occursAt);
        }

        $qb->delete();
    }

    public function computeOccurrenceOverrides(string $occursAt, array $attributes, ?RecurrenceException $exception): array
    {
        $overrides = [];
        $dateKeys = $this->listDateAttributes();
        $dtstart = $this->getAttribute($this->recurrenceStartKey());

        foreach ($attributes as $key => $value) {
            if (in_array($key, $dateKeys)) {
                $attribute = $this->getAttribute($key);

                if (is_null($attribute)) {
                    $overrides[$key] = $value;

                    continue;
                }

                $offset = $dtstart->diffInDays($attribute);
                $exceptionOverrides = $exception->overrides ?? [];

                if (Arr::exists($exceptionOverrides, $key)) {
                    $current = Carbon::parse($exceptionOverrides[$key]);
                } else {
                    $current = Carbon::parse($occursAt)->addDays($offset)->setTimeFrom($attribute);
                }

                if ($current->ne($value)) {
                    $overrides[$key] = $value;
                }
            } else {
                $current = $exception?->overrides[$key] ?? $this->getAttribute($key);

                if ($current !== $value) {
                    $overrides[$key] = $value;
                }
            }
        }

        return $overrides;
    }

    public function cancelOccurrence(string $occursAt): void
    {
        $this->recurrenceExceptions()->updateOrCreate(
            ['occurs_at' => $occursAt],
            ['is_cancelled' => true, 'overrides' => []]
        );
    }

    public function normalizeRecurringDataForUpdate(array &$data, string $occursAt): void
    {
        if (Carbon::parse($data[$this->recurrenceStartKey()])->isSameDay($occursAt)) {
            $start = $this->getAttribute($this->recurrenceStartKey());

            foreach ($this->listDateAttributes() as $key) {
                if (isset($data[$key])) {
                    $data[$key] = Carbon::parse($data[$key])->setDateFrom($start);
                }
            }
        }
    }

    protected function listDateAttributes(): array
    {
        return collect($this->getCasts())
            ->filter(fn ($cast) => str_starts_with($cast, 'date'))
            ->keys()
            ->toArray();
    }
}
