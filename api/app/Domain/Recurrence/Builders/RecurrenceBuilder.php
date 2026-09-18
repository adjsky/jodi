<?php

declare(strict_types=1);

namespace App\Domain\Recurrence\Builders;

use App\Domain\Recurrence\Contracts\Recurrable;
use App\Support\Builders\JodiBuilder;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model&Recurrable
 *
 * @extends JodiBuilder<TModel>
 */
class RecurrenceBuilder extends JodiBuilder
{
    public function withPossibleOccurrencesBetween(CarbonInterface $viewStart, CarbonInterface $viewEnd): static
    {

        return $this
            ->with('recurrenceExceptions')
            ->where(function ($query) use ($viewStart, $viewEnd) {
                $query
                    ->where(function ($query) use ($viewStart, $viewEnd) {
                        $query->whereNull('rrule');

                        $startKey = $this->model->recurrenceStartKey();
                        $endKey = $this->model->recurrenceEndKey();

                        if ($endKey) {
                            $query
                                ->where($startKey, '<=', $viewEnd)
                                ->where($endKey, '>', $viewStart);
                        } else {
                            $query->whereBetween($startKey, [$viewStart, $viewEnd]);
                        }
                    })
                    ->orWhere(function ($query) use ($viewEnd) {
                        $query
                            ->whereNotNull('rrule')
                            ->where($this->model->recurrenceStartKey(), '<=', $viewEnd);
                    });
            });
    }
}
