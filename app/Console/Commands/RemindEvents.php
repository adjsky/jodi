<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Event;
use App\Notifications\EventReminder;
use Illuminate\Console\Command;

class RemindEvents extends Command
{
    protected $signature = 'jodi:remind:events';

    protected $description = 'Remind users about planned events.';

    public function handle(): void
    {
        Event::with('user')
            ->whereDate('notify_at', '<=', now())
            ->where('notify_status', '!=', 'sent')
            ->orWhereNull('notify_status')
            ->chunk(100, function ($events) {
                foreach ($events as $event) {
                    if (! $event->user) {
                        continue;
                    }

                    $event->user->notify(new EventReminder);
                    $event->notify_status = 'processing';
                    $event->save();
                }
            });
    }
}
