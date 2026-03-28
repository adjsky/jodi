<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Reminder\Notifications\TodoReminder;
use App\Models\Todo;
use Illuminate\Console\Command;

class RemindTodosCommand extends Command
{
    protected $signature = 'jodi:remind:todos';

    protected $description = 'Remind users about planned todos.';

    public function handle(): void
    {
        Todo::remind(TodoReminder::class);
    }
}
