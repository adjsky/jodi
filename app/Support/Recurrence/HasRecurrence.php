<?php

declare(strict_types=1);

namespace App\Support\Recurrence;

use App\Models\RecurrenceException;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use RRule\RRule;

/**
 * @property ?Carbon $occurs_at
 * @property ?string $recurring_since
 */
trait HasRecurrence
{
    abstract protected function recurrenceStartKey(): string;

    /** @return array[string] */
    abstract protected function recurrenceDateKeys(): array;

    /** @return MorphMany<RecurrenceException,$this> */
    abstract public function recurrenceExceptions(): MorphMany;

    #[Scope]
    protected function withPossibleOccurrencesBetween(Builder $query, CarbonInterface $viewStart, CarbonInterface $viewEnd): void
    {
        $query
            ->where(function ($query) use ($viewStart, $viewEnd) {
                $query
                    ->whereNull('rrule')
                    ->whereBetween($this->recurrenceStartKey(), [$viewStart, $viewEnd]);
            })
            ->orWhere(function ($query) use ($viewEnd) {
                $query
                    ->whereNotNull('rrule')
                    ->where($this->recurrenceStartKey(), '<=', $viewEnd);
            });
    }

    /**
     * @return Collection<int, static>
     */
    public function occurrencesBetween(CarbonInterface $viewStart, CarbonInterface $viewEnd): Collection
    {
        if (! $this->rrule) {
            $model = $this->replicate();
            $model->id = $this->id;

            return collect([$model]);
        }

        $exceptions = $this->recurrenceExceptions()
            ->whereBetween('occurs_at', [$viewStart, $viewEnd])
            ->get()
            ->keyBy(fn ($e) => $e->occurs_at->toDateString());

        $dtstart = $this->getAttribute($this->recurrenceStartKey())->toDateString();

        $rrule = new RRule([
            ...RRule::parseRfcString($this->rrule),
            'DTSTART' => $dtstart,
        ]);

        return collect($rrule->getOccurrencesBetween($viewStart, $viewEnd))
            ->map(function ($date) use ($dtstart, $exceptions) {
                $exception = $exceptions->get(Carbon::instance($date)->toDateString());

                if ($exception?->is_cancelled) {
                    return null;
                }

                $model = $this->replicate();
                $model->id = $this->id;
                $model->occurs_at = Carbon::instance($date);
                $model->recurring_since = $dtstart;

                $dateKeys = [$this->recurrenceStartKey(), ...$this->recurrenceDateKeys()];

                foreach ($dateKeys as $key) {
                    if (isset($exception->overrides[$key])) {
                        continue;
                    }

                    $attribute = $this->getAttribute($key);

                    if (! $attribute) {
                        continue;
                    }

                    $model->setAttribute(
                        $key,
                        Carbon::instance($date)->setTimeFrom($attribute)
                    );
                }

                if ($exception) {
                    foreach ($exception->overrides as $key => $value) {
                        $model->setAttribute($key, $value);
                    }
                }

                return $model;
            })
            ->filter()
            ->values();
    }

    public function applyException(string $occursAt, array $overrides, ?RecurrenceException $existingException): void
    {
        $occursAt = Carbon::parse($occursAt)->toDateString();

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
            ->where('occurs_at', Carbon::parse($occursAt)->toDateString())
            ->first();
    }

    public function deleteExceptions(?string $occursAt = null): void
    {
        $qb = $this->recurrenceExceptions();

        if (! is_null($occursAt)) {
            $qb = $qb->where('occurs_at', Carbon::parse($occursAt)->toDateString());
        }

        $qb->delete();
    }

    public function computeOccurenceOverrides(string $occursAt, array $attributes, ?RecurrenceException $exception): array
    {
        $overrides = [];
        $dateKeys = [$this->recurrenceStartKey(), ...$this->recurrenceDateKeys()];

        foreach ($attributes as $key => $value) {
            $current = $exception?->overrides[$key] ?? $this->getAttribute($key);

            if (in_array($key, $dateKeys)) {
                $currentDate = Carbon::parse($exception?->overrides[$key] ?? $this->getAttribute($key)->setDateFrom($occursAt));

                if ($currentDate->ne($value)) {
                    $overrides[$key] = $value;
                }
            } else {
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
            ['occurs_at' => Carbon::parse($occursAt)->toDateString()],
            ['is_cancelled' => true, 'overrides' => []]
        );
    }

    public function normalizeRecurringDataForUpdate(array $data): array
    {
        $start = $this->getAttribute($this->recurrenceStartKey());
        $dateKeys = [$this->recurrenceStartKey(), ...$this->recurrenceDateKeys()];

        foreach ($dateKeys as $key) {
            if (isset($data[$key])) {
                $data[$key] = Carbon::parse($data[$key])->setDateFrom($start);
            }
        }

        return $data;
    }
}
