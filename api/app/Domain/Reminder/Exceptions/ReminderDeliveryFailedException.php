<?php

declare(strict_types=1);

namespace App\Domain\Reminder\Exceptions;

use App\Support\Exceptions\JodiException;
use Illuminate\Support\Collection;
use Kreait\Firebase\Exception\MessagingException;

class ReminderDeliveryFailedException extends JodiException
{
    /**
     * @param  Collection<int, MessagingException>  $errors
     * */
    public function __construct(
        private string $notification,
        private string $model,
        private int $modelId,
        private int $deliveryAttempts,
        private Collection $errors
    ) {
        parent::__construct('Reminder delivery failed after exhausting retries.');
    }

    public function context(): array
    {
        return [
            'notification' => $this->notification,
            'model' => $this->model,
            'model_id' => $this->modelId,
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
