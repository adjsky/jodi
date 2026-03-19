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
        if (is_null($this->rrule)) {
            $model = $this->replicate();
            $model->id = $this->id;

            return collect([$model]);
        }

        $exceptions = $this->recurrenceExceptions()
            ->whereIn('occurs_at', [
                $viewStart->toDateString(),
                $viewEnd->toDateString(),
            ])
            ->get()
            ->keyBy(fn ($e) => $e->occurs_at->toDateString());

        $dtstart = $this->getAttribute($this->recurrenceStartKey());

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
                $model->recurring_since = $dtstart->toDateString();

                $dateKeys = [$this->recurrenceStartKey(), ...$this->recurrenceDateKeys()];

                foreach ($dateKeys as $key) {
                    if (isset($exception->overrides[$key])) {
                        continue;
                    }

                    $attribute = $this->getAttribute($key);

                    if (is_null($attribute)) {
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

    public function computeOccurenceOverrides(string $occursAt, array $attributes, ?RecurrenceException $exception): array
    {
        $overrides = [];
        $dateKeys = [$this->recurrenceStartKey(), ...$this->recurrenceDateKeys()];

        foreach ($attributes as $key => $value) {
            $current = $exception?->overrides[$key] ?? $this->getAttribute($key);

            if (in_array($key, $dateKeys)) {
                $attribute = $this->getAttribute($key);

                if (is_null($attribute)) {
                    $overrides[$key] = $value;

                    continue;
                }

                $currentDate = Carbon::parse($exception?->overrides[$key] ?? $attribute->setDateFrom($occursAt));

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
            ['occurs_at' => $occursAt],
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
