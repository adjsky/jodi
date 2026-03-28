<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Reminder\Notifications\EventReminder;
use App\Models\Event;
use Illuminate\Console\Command;

class RemindEventsCommand extends Command
{
    protected $signature = 'jodi:remind:events';

    protected $description = 'Remind users about planned events.';

    public function handle(): void
    {
        Event::remind(EventReminder::class);
    }
}
