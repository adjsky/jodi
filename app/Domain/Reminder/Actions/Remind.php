<?php

declare(strict_types=1);

namespace App\Domain\Reminder\Actions;

use App\Domain\Event\Models\Event;
use App\Domain\Todo\Models\Todo;
use App\Support\Actions\JodiAction;

class Remind extends JodiAction
{
    /**
     * @template TModel of Todo|Event
     *
     * @param  class-string<TModel>  $model
     * @param  class-string  $notification
     */
    public function handle($model, string $notification): void
    {
        $start = now();
        $end = $start->copy()->addDays(config('jodi.reminders.window.days'));

        $models = $model::query()
            ->withPossibleOccurrencesBetween($start, $end)
            ->with('user')
            ->where('notify_status', '=', 'waiting')
            ->get();

        foreach ($models as $m) {
            foreach ($m->occurrencesBetween($start, $end) as $occurrence) {
                $notifyAt = $occurrence->getAttribute('notify_at');
                $startsAt = $occurrence->getAttribute($occurrence->recurrenceStartKey());

                if ($notifyAt->gt($start) || $startsAt->lte($start)) {
                    continue;
                }
                if ($occurrence->notify_status != 'waiting') {
                    continue;
                }

                $m->user->notify(new $notification($occurrence, $occurrence->occurs_at));

                if ($occurrence->occurs_at) {
                    $m->applyException(
                        $occurrence->occurs_at,
                        ['notify_status' => 'processing'],
                        $m->findException($occurrence->occurs_at)
                    );
                } else {
                    $m->update(['notify_status' => 'processing']);
                }
            }
        }
    }
}
