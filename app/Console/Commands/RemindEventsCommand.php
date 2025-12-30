<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Event\Notifications\EventReminder;
use App\Models\Event;
use Illuminate\Console\Command;

class RemindEventsCommand extends Command
{
    protected $signature = 'jodi:remind:events';

    protected $description = 'Remind users about planned events.';

    public function handle(): void
    {
        Event::with('user')
            ->whereDate('notify_at', '<=', now())
            ->whereDate('starts_at', '<=', now())
            ->where('notify_status', '!=', 'sent')
            ->chunk(100, function ($events) {
                foreach ($events as $event) {
                    if (! $event->user) {
                        continue;
                    }

                    $event->user->notify(new EventReminder($event));
                    $event->notify_status = 'processing';
                    $event->save();
                }
            });
    }
}
