<?php

declare(strict_types=1);

namespace App\Support\Firebase\Concerns;

use App\Support\Firebase\Exceptions\NotificationDeliveryFailedException;
use App\Support\Retry\Jitter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Kreait\Firebase\Exception\Messaging\ApiConnectionFailed;
use Kreait\Firebase\Exception\Messaging\MessagingError;
use Kreait\Firebase\Exception\Messaging\QuotaExceeded;
use Kreait\Firebase\Exception\Messaging\ServerError;
use Kreait\Firebase\Exception\Messaging\ServerUnavailable;
use Kreait\Firebase\Messaging\MulticastSendReport;
use NotificationChannels\Fcm\FcmChannel;

/**
 * @phpstan-require-implements ShouldQueue
 */
trait RetriesFcmDeliveryFailure
{
    use Queueable;

    protected const MAX_DELIVERY_ATTEMPTS = 3;

    protected int $deliveryAttempts = 1;

    public function afterSending(
        mixed $notifiable,
        string $channel,
        mixed $response,
    ): void {
        if ($channel !== FcmChannel::class || $response === null) {
            return;
        }

        if (! $response instanceof Collection) {
            report(new \UnexpectedValueException(
                sprintf(
                    'Expected FCM response to be %s|null, received %s.',
                    Collection::class,
                    get_debug_type($response),
                ),
            ));

            return;
        }

        /** @var Collection<int, MulticastSendReport> $reports */
        $reports = $response;

        $hasSuccess = $reports->some(
            fn ($report) => $report->successes()->count() > 0,
        );

        if ($hasSuccess) {
            return;
        }

        $errors = $reports
            ->flatMap(fn ($report) => $report->failures()->getItems())
            ->map(fn ($report) => $report->error())
            ->filter();

        if ($this->deliveryAttempts >= self::MAX_DELIVERY_ATTEMPTS) {
            report(new NotificationDeliveryFailedException(
                $this::class,
                $this->deliveryAttempts,
                $errors,
            ));

            return;
        }

        $isRetryable = $errors->some(function ($error) {
            if ($error instanceof MessagingError) {
                return $this->deliveryAttempts <= 1;
            }

            return (
                $error instanceof QuotaExceeded
                || $error instanceof ServerUnavailable
                || $error instanceof ServerError
                || $error instanceof ApiConnectionFailed
            );
        });

        if (! $isRetryable) {
            return;
        }

        $retryAfter = $errors
            ->map(function ($error) {
                if (
                    $error instanceof QuotaExceeded
                    || $error instanceof ServerUnavailable
                ) {
                    return $error->retryAfter();
                }

                return null;
            })
            ->filter()
            ->sortByDesc(fn ($date) => $date->getTimestamp())
            ->first(default: Jitter::equal($this->deliveryAttempts, base: 5));

        $notification = clone $this;
        $notification->deliveryAttempts += 1;

        $notifiable->notify($notification->delay($retryAfter));
    }
}
