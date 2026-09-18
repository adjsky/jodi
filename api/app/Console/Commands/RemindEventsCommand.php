<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Event\Models\Event;
use App\Domain\Reminder\Actions\Remind;
use App\Domain\Reminder\Notifications\EventReminder;
use App\Support\Commands\JodiCommand;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Signature('jodi:remind:events')]
#[Description('Remind users about planned events.')]
class RemindEventsCommand extends JodiCommand
{
    public function handle(): void
    {
        Remind::make()->handle(Event::class, EventReminder::class);
    }
}
