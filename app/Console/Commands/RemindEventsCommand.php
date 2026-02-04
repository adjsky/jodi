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
            ->where('notify_at', '<=', now())
            ->where('starts_at', '<=', now())
            ->where('notify_status', '!=', 'sent')
            ->chunk(100, function ($events) {
                $sent = [];

                foreach ($events as $event) {
                    if (! $event->user) {
                        continue;
                    }

                    $event->user->notify(new EventReminder($event));
                    $sent[] = $event->id;
                }

                Event::whereIn('id', $sent)->update(['notify_status' => 'processing']);
            });
    }
}
