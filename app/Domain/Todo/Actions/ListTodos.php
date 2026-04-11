<?php

declare(strict_types=1);

namespace App\Domain\Todo\Actions;

use App\Domain\Todo\Data\Output\TodoData;
use App\Domain\Todo\Models\Todo;
use App\Support\Actions\Action;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ListTodos extends Action
{
    public function handle(string $date, Carbon $start, Carbon $end): Collection
    {
        /** @var Collection<int, Todo> */
        $todos = $this->user()->todos()
            ->with([
                'category',
                'positions' => fn ($q) => $q->where('date', $date),
            ])
            ->withPossibleOccurrencesBetween($start, $end)
            ->get()
            ->flatMap(fn ($t) => $t->occurrencesBetween($start, $end));

        return TodoData::collect(
            $todos
                ->each(fn ($t) => $t->setAttribute(
                    'nth',
                    $t->positions->first()->position ?? PHP_INT_MAX
                ))
                ->sortBy([
                    ['category.name', 'asc'],
                    ['nth', 'asc'],
                    ['created_at', 'asc'],
                ])
                ->values()
        );
    }
}
