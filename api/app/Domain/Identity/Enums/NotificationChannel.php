<?php

declare(strict_types=1);

namespace App\Domain\Identity\Enums;

enum NotificationChannel: string
{
    case Push = 'push';

    case Mail = 'mail';
}
