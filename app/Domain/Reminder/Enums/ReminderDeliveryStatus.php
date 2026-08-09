<?php

declare(strict_types=1);

namespace App\Domain\Reminder\Enums;

enum ReminderDeliveryStatus: string
{
    case Waiting = 'waiting';
    case Processing = 'processing';
    case Sent = 'sent';
    case Failed = 'failed';
}
