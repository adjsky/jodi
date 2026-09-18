<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Reminder\Actions\Remind;
use App\Domain\Reminder\Notifications\TodoReminder;
use App\Domain\Todo\Models\Todo;
use App\Support\Commands\JodiCommand;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Signature('jodi:remind:todos')]
#[Description('Remind users about planned todos.')]
class RemindTodosCommand extends JodiCommand
{
    public function handle(): void
    {
        Remind::make()->handle(Todo::class, TodoReminder::class);
    }
}
