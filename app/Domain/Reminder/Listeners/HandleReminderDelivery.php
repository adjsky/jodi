<?php

declare(strict_types=1);

namespace App\Domain\Reminder\Listeners;

use App\Domain\Reminder\Enums\ReminderDeliveryStatus;
use App\Domain\Reminder\Exceptions\ReminderDeliveryFailedException;
use App\Domain\Reminder\Notifications\EventReminder;
use App\Domain\Reminder\Notifications\TodoReminder;
use App\Support\Retry\Jitter;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Collection;
use Kreait\Firebase\Exception\Messaging\ApiConnectionFailed;
use Kreait\Firebase\Exception\Messaging\MessagingError;
use Kreait\Firebase\Exception\Messaging\QuotaExceeded;
use Kreait\Firebase\Exception\Messaging\ServerError;
use Kreait\Firebase\Exception\Messaging\ServerUnavailable;
use Kreait\Firebase\Messaging\MulticastSendReport;
use NotificationChannels\Fcm\FcmChannel;

class HandleReminderDelivery
{
    public function __construct() {}

    public function handle(NotificationSent $event): void
    {
        if (
            ! $event->notification instanceof EventReminder &&
            ! $event->notification instanceof TodoReminder
        ) {
            return;
        }

        $notification = $event->notification;

        if ($event->channel == 'mail') {
            $this->updateStatus($notification, ReminderDeliveryStatus::Sent);

            return;
        }

        if ($event->channel != FcmChannel::class) {
            return;
        }

        if ($event->response == null) {
            $this->updateStatus($notification, ReminderDeliveryStatus::Failed);

            return;
        }

        if (! $event->response instanceof Collection) {
            report(new \UnexpectedValueException(
                sprintf(
                    'Expected FCM response to be %s|null, received %s.',
                    Collection::class,
                    get_debug_type($event->response)
                ))
            );

            $this->updateStatus($notification, ReminderDeliveryStatus::Failed);

            return;
        }

        /** @var Collection<int, MulticastSendReport> */
        $reports = $event->response;

        $hasSuccess = $reports->some(fn ($report) => $report->successes()->count() > 0);

        if ($hasSuccess) {
            $this->updateStatus($notification, ReminderDeliveryStatus::Sent);

            return;
        }

        $errors = $reports
            ->flatMap(fn ($report) => $report->failures()->getItems())
            ->map(fn ($report) => $report->error())
            ->filter();

        if ($notification->deliveryAttempts >= $notification::MAX_DELIVERY_ATTEMPTS) {
            report(new ReminderDeliveryFailedException(
                $notification::class,
                $notification->model::class,
                $notification->model->getKey(),
                $notification->deliveryAttempts,
                $errors
            ));

            $this->updateStatus($notification, ReminderDeliveryStatus::Failed);

            return;
        }

        $isRetryable = $errors->some(function ($error) use ($notification) {
            if ($error instanceof MessagingError) {
                return $notification->deliveryAttempts <= 1;
            }

            return
                $error instanceof QuotaExceeded ||
                $error instanceof ServerUnavailable ||
                $error instanceof ServerError ||
                $error instanceof ApiConnectionFailed;
        });

        if (! $isRetryable) {
            $this->updateStatus($notification, ReminderDeliveryStatus::Failed);

            return;
        }

        $retryAfter = $errors
            ->map(function ($error) {
                if ($error instanceof QuotaExceeded || $error instanceof ServerUnavailable) {
                    return $error->retryAfter();
                }

                return null;
            })
            ->filter()
            ->sortByDesc(fn ($date) => $date->getTimestamp())
            ->first(default: Jitter::equal($notification->deliveryAttempts, base: 5));

        $notification = clone $notification;
        $notification->deliveryAttempts += 1;

        $event->notifiable->notify($notification->delay($retryAfter));
    }

    private function updateStatus(
        EventReminder|TodoReminder $notification,
        ReminderDeliveryStatus $status
    ): void {
        $model = $notification->model;
        $occursAt = $notification->occursAt;

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
