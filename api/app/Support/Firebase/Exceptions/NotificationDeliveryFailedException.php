<?php

declare(strict_types=1);

namespace App\Support\Firebase\Exceptions;

use App\Support\Exceptions\JodiException;
use Illuminate\Support\Collection;
use Kreait\Firebase\Exception\MessagingException;

class NotificationDeliveryFailedException extends JodiException
{
    /**
     * @param  Collection<int, MessagingException>  $errors
     * */
    public function __construct(
        private string $notification,
        private int $deliveryAttempts,
        private Collection $errors,
    ) {
        parent::__construct(
            'Notification delivery failed after exhausting retries.',
        );
    }

    public function context(): array
    {
        return [
            'notification' => $this->notification,
            'delivery_attempts' => $this->deliveryAttempts,
            'error_counts' => $this->buildErrorCounts(),
        ];
    }

    private function buildErrorCounts(): array
    {
        return $this->errors
            ->map(fn ($error) => $error::class)
            ->countBy()
            ->all();
    }
}
