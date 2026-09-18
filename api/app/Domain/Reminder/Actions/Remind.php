<?php

declare(strict_types=1);

namespace App\Domain\Reminder\Actions;

use App\Domain\Event\Models\Event;
use App\Domain\Recurrence\Builders\RecurrenceBuilder;
use App\Domain\Reminder\Enums\ReminderDeliveryStatus;
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
    public function handle($model, $notification): void
    {
        $start = now();
        $end = $start->addDays(config('jodi.reminders.window.days'));

        /** @var RecurrenceBuilder<TModel> */
        $query = $model::query();

        $models = $query
            ->withPossibleOccurrencesBetween($start, $end)
            ->with('user')
            ->where('notify_status', '=', ReminderDeliveryStatus::Waiting)
            ->get();

        foreach ($models as $m) {
            foreach ($m->occurrencesBetween($start, $end) as $occurrence) {
                $notifyAt = $occurrence->getAttribute('notify_at');
                $startsAt = $occurrence->getAttribute($occurrence->recurrenceStartKey());

                if ($notifyAt->gt($start) || $startsAt->lte($start)) {
                    continue;
                }
                if ($occurrence->notify_status != ReminderDeliveryStatus::Waiting) {
                    continue;
                }

                $this->updateStatus(
                    $m,
                    $occurrence->occurs_at,
                    ReminderDeliveryStatus::Processing
                );

                try {
                    $m->user->notify(new $notification($occurrence, $occurrence->occurs_at));
                } catch (\Throwable $dispatchException) {
                    try {
                        $this->updateStatus(
                            $m,
                            $occurrence->occurs_at,
                            ReminderDeliveryStatus::Waiting
                        );
                    } catch (\Throwable $updateException) {
                        report($updateException);
                    }

                    throw $dispatchException;
                }
            }
        }
    }

    /**
     * @param  Todo|Event  $model
     */
    private function updateStatus(
        $model,
        ?string $occursAt,
        ReminderDeliveryStatus $status
    ): void {
        if ($occursAt) {
            $model->applyException(
                $occursAt,
                ['notify_status' => $status],
                $model->findException($occursAt)
            );
        } else {
            $model->update(['notify_status' => $status]);
        }
    }
}
