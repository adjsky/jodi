<?php

declare(strict_types=1);

namespace App\Support\Recurrence;

use App\Models\RecurrenceException;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use RRule\RRule;

/**
 * @property ?Carbon $occurs_at
 */
trait HasRecurrence
{
    abstract protected function recurrenceStartColumn(): string;

    /** @return array[string] */
    abstract protected function recurrenceDateColumns(): array;

    /** @return MorphMany<RecurrenceException,$this> */
    abstract public function recurrenceExceptions(): MorphMany;

    #[Scope]
    protected function withPossibleOccurrencesBetween(Builder $query, CarbonInterface $viewStart, CarbonInterface $viewEnd): void
    {

        $query
            ->where(function ($query) use ($viewStart, $viewEnd) {
                $query
                    ->whereNull('rrule')
                    ->whereBetween($this->recurrenceStartColumn(), [$viewStart, $viewEnd]);
            })
            ->orWhere(function ($query) use ($viewEnd) {
                $query
                    ->whereNotNull('rrule')
                    ->where($this->recurrenceStartColumn(), '<=', $viewEnd);
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

        $rrule = new RRule([
            ...RRule::parseRfcString($this->rrule),
            'DTSTART' => $this->getAttribute($this->recurrenceStartColumn())->toDateString(),
        ]);

        return collect($rrule->getOccurrencesBetween($viewStart, $viewEnd))
            ->map(function ($date) use ($exceptions) {
                $exception = $exceptions->get(Carbon::instance($date)->toDateString());

                if ($exception?->is_cancelled) {
                    return null;
                }

                $model = $this->replicate();
                $model->id = $this->id;
                $model->occurs_at = Carbon::instance($date);

                $dateColumns = [$this->recurrenceStartColumn(), ...$this->recurrenceDateColumns()];

                foreach ($dateColumns as $column) {
                    if (isset($exception->overrides[$column])) {
                        continue;
                    }

                    $attribute = $this->getAttribute($column);

                    if (! $attribute) {
                        continue;
                    }

                    $model->setAttribute(
                        $column,
                        CarbonImmutable::instance($date)->setTimeFrom($attribute)
                    );
                }

                if ($exception) {
                    foreach ($exception->overrides as $column => $value) {
                        $model->setAttribute($column, $value);
                    }
                }

                return $model;
            })
            ->filter()
            ->values();
    }

    public function applyException(string $occursAt, array $overrides): void
    {
        $this->recurrenceExceptions()->updateOrCreate(
            ['occurs_at' => Carbon::parse($occursAt)->toDateString()],
            ['is_cancelled' => false, 'overrides' => $overrides]
        );
    }

    public function cancelOccurrence(string $occursAt): void
    {
        $this->recurrenceExceptions()->updateOrCreate(
            ['occurs_at' => Carbon::parse($occursAt)->toDateString()],
            ['is_cancelled' => true, 'overrides' => []]
        );
    }

    public function deleteExceptions(): void
    {
        $this->recurrenceExceptions()->delete();
    }
}
