<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Todo\Notifications\TodoReminder;
use App\Models\Todo;
use Illuminate\Console\Command;

class RemindTodosCommand extends Command
{
    protected $signature = 'jodi:remind:todos';

    protected $description = 'Remind users about planned todos.';

    public function handle(): void
    {
        Todo::with('user')
            ->whereDate('notify_at', '<=', now())
            ->whereDate('scheduled_at', '<=', now())
            ->where('notify_status', '!=', 'sent')
            ->chunk(100, function ($todos) {
                foreach ($todos as $todo) {
                    if (! $todo->user) {
                        continue;
                    }

                    $todo->user->notify(new TodoReminder($todo));
                    $todo->notify_status = 'processing';
                    $todo->save();
                }
            });
    }
}
