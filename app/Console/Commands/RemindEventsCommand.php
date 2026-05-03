<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Event\Models\Event;
use App\Domain\Reminder\Actions\Remind;
use App\Domain\Reminder\Notifications\EventReminder;
use App\Support\Commands\JodiCommand;

class RemindEventsCommand extends JodiCommand
{
    protected $signature = 'jodi:remind:events';

    protected $description = 'Remind users about planned events.';

    public function handle(): void
    {
        Remind::make()->handle(Event::class, EventReminder::class);
    }
}
