<?php

declare(strict_types=1);

namespace App\Domain\Recurrence\Contracts;

use App\Domain\Recurrence\Models\RecurrenceException;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

interface Recurrable
{
    public function recurrenceStartKey(): string;

    /**
     * @return Collection<int, static>
     */
    public function occurrencesBetween(CarbonInterface $viewStart, CarbonInterface $viewEnd): Collection;

    public function applyException(string $occursAt, array $overrides, ?RecurrenceException $existingException): void;

    public function findException(string $occursAt): ?RecurrenceException;

    public function deleteExceptions(?string $occursAt = null): void;

    public function computeOccurrenceOverrides(string $occursAt, array $attributes, ?RecurrenceException $exception): array;

    public function cancelOccurrence(string $occursAt): void;

    public function normalizeRecurringDataForUpdate(array &$data, string $occursAt): void;
}
