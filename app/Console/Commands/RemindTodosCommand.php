<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Reminder\Actions\Remind;
use App\Domain\Reminder\Notifications\TodoReminder;
use App\Domain\Todo\Models\Todo;
use App\Support\Commands\JodiCommand;

class RemindTodosCommand extends JodiCommand
{
    protected $signature = 'jodi:remind:todos';

    protected $description = 'Remind users about planned todos.';

    public function handle(): void
    {
        Remind::make()->handle(Todo::class, TodoReminder::class);
    }
}
